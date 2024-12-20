<?php

if (!defined('SORT_LOCALE_STRING'))
    define('SORT_LOCALE_STRING', 5);
if (!defined('SORT_NAT'))
    define('SORT_NAT', 16);
if (!defined('SORT_TIME'))
    define('SORT_TIME', 17);
if (!defined('SORT_NULL'))
    define('SORT_NULL', 32);
class MyCSV
{

    var $fields = array("id");
    var $data = array();
    var $delimiter = ",";
    var $insert_id = null;
    var $filename = "";
    var $_fp = false;
    var $_limitRows = null;

    private static $lockedFiles = []; // Store locked files

    function MyCSV($tablename = "", $length = 10000)
    {
        if ($tablename) $this->read($tablename, $length);
    }
   function read(string $tablename, int $length = 10000): bool
    {
        $this->filename = $tablename;
        
        // Ensure a valid file extension
        if (!preg_match('/\.\w+$/', $this->filename)) {
            $this->filename .= ".csv";
        }

        // Check file existence and URL
        if (!strpos($this->filename, "://") && !file_exists($this->filename)) {
            return false;
        }

        // Check if the file is already locked
        if (isset(self::$lockedFiles[$this->filename])) {
            throw new Exception("MyCSV::read() failed, file {$this->filename} is open already");
        }

        // Lock the file
        self::$lockedFiles[$this->filename] = true;

        // Try to open the file with read/write access, else open in read-only mode
        $this->_fp = $this->openFile($this->filename);
        if (!$this->_fp) {
            return false;
        }

        // File locking (exclusive lock for local files)
        if (!strpos($this->filename, "://")) {
            flock($this->_fp, LOCK_EX);
        }

        // Read the CSV headers and determine the delimiter
        $this->fields = $this->getCsvFields($length);
        if (!$this->fields) {
            return false;
        }

        // Detect the correct delimiter if necessary
        $this->detectDelimiter($length);

        // Process each row
        $this->processRows($length);

        // Unlock the file
        unset(self::$lockedFiles[$this->filename]);

        return true;
    }

     function openFile(string $filename)
    {
        if (is_writable($filename)) {
            return fopen($filename, "r+b");
        }
        return fopen($filename, "rb");
    }

     function getCsvFields(int $length)
    {
        return fgetcsv($this->_fp, $length, $this->delimiter);
    }

     function detectDelimiter(int $length)
    {
        $delimiters = str_replace($this->delimiter, "", ",;\t\0|&: ") . $this->delimiter;

        while (count($this->fields) < 2) {
            $this->delimiter = $delimiters[0];
            if (strlen($delimiters) > 1) {
                $delimiters = substr($delimiters, 1);
            } else {
                break;
            }

            rewind($this->_fp);
            $this->fields = $this->getCsvFields($length);
        }
    }

     function processRows(int $length)
    {
        // Find the index of the "id" field (case insensitive)
        $idColumnIndex = array_search("id", array_map('strtolower', $this->fields), true);
        if ($idColumnIndex === false) {
            $idColumnIndex = count($this->fields); // If no "id" column, generate new ids
        }

        $lastId = 0;
        $fieldsCount = count($this->fields);

        while ($row = fgetcsv($this->_fp, $length, $this->delimiter)) {
            // Generate an ID if necessary
            $id = isset($row[$idColumnIndex]) ? $row[$idColumnIndex] : $lastId + 1;
            $lastId = max($id, $lastId);

            // Process each column and store the data
            $count = min($fieldsCount, count($row));
            for ($c = 0; $c < $count; ++$c) {
                $row[$c] = strtr($row[$c], ["\\\x7F" => "\x00", "\\\x93" => '"', '\\\\' => '\\']);
                $this->data[$id][$this->fields[$c]] = $row[$c];
            }
        }
    }
    function add_field($field, $afterField = null)
    {
        if (!preg_match('/^[\w\x7F-\xFF]+$/is', $field) || in_array($field, $this->fields))
        {
            return false;
        }
        if (isset($afterField) && in_array($afterField, $this->fields))
        {
            $newFields = array();
            foreach ($this->fields as $oldField)
            {
                $newFields[] = $oldField;
                if (strcasecmp($oldField, $afterField) == 0) $newFields[] = $field;
            }
            $this->fields = $newFields;
        }
        else $this->fields[] = $field;
        return true;
    }
    function data_seek($row_number)
    {
        return $this->seek($row_number, SEEK_SET);
    }
    function delete($id = null)
    {
        if (is_array($id) && isset($id['id'])) $id = $id['id'];
        if (isset($id))
        {
            if (!is_array($id)) unset($this->data[$id]);
        }
        else
        {
            $this->data = array();
            ++$this->insert_id;
        }
    }
    function drop_field($field)
{
    if (is_array($field) || strcasecmp($field, "id") == 0) return false;
    $offset = array_search($field, $this->fields);
    
    if ($offset === false || $offset === null) return false;

    array_splice($this->fields, $offset, 1);

    foreach ($this->data as $id => $row) {
        if (isset($this->data[$id][$field])) {
            unset($this->data[$id][$field]);
        }
    }
    reset($this->data);
    return true;
}
    function drop_table()
    {
        $this->fields = array("id");
        $this->data = array();
        $this->insert_id = null;
    }
    function fetch_assoc()
    {
        return $this->each();
    }
    function insert($data)
    {
        if (!is_array($data)) return false;
        if (isset($data['id']) && strlen($data['id']))
        {
            $this->insert_id = $data['id'];
        }
        elseif (!isset($this->insert_id) && empty($this->data))
        {
            $this->insert_id = 1;
        }
        if (isset($this->data[$this->insert_id])) $this->insert_id += 1;
        if (!isset($this->insert_id) || isset($this->data[$this->insert_id]))
        {
            $this->insert_id = max(array_keys($this->data)) + 1;
        }
        $this->data[$this->insert_id] = $data;
        if (empty($this->fields) || count($this->fields) < 2)
        {
            unset($data['id']);
            $this->fields = array_merge(array("id"), array_keys($data));
        }
    }
    function insert_id()
    {
        return isset($this->insert_id) ? $this->insert_id : false;
    }

    function join(&$rightTable, $foreignKey)
    {
        if (is_array($rightTable)) $rightData = $rightTable;
        else
        {
            $rightData = $rightTable->data;
            $prefix = preg_replace('/\.\w+$/', '', basename($rightTable->filename));
        }
        reset($this->data);
        foreach ($this->data as $id => $value)
        {
            if (strcasecmp($foreignKey, "id") == 0) $fid = $id;
            else $fid = $this->data[$id][$foreignKey];
            if (isset($rightData[$fid]))
            {
                if (!empty($prefix) && !isset($rightData[$fid][$prefix . ".id"]))
                {
                    foreach ($rightData[$fid] as $field => $value)
                    {
                        $rightData[$fid][$prefix . "." . $field] = &$rightData[$fid][$field];
                    }
                }
                $this->data[$id] += $rightData[$fid];
            }
        }
        reset($this->data);
    }
    function limit($rows = null, $id = null, $whence = null)
    {
        $this->_limitRows = $rows > 0 ? $rows : null;
        return isset($id) ? $this->seek($id, $whence) : $this->reset();
    }
    function num_rows()
    {
        return count($this->data);
    }
    function tablename()
    {
        return preg_replace('{^\./|\.csv$}', '', $this->filename);
    }
    function update($data, $id = null)
    {
        if (!is_array($data)) return false;
        if (!isset($data['id']) && !isset($id)) return false;
        if (!isset($data['id'])) $data['id'] = $id;
        elseif (!isset($id)) $id = $data['id'];
        elseif (strcmp($data['id'], $id) != 0 && isset($this->data[$data['id']]))
        {
            return false;
        }
        $this->data[$data['id']] = $data + (array)$this->data[$id];
        if (strcmp($data['id'], $id) != 0) unset($this->data[$id]);
        return true;
    }
    function count()
    {
        return count($this->data);
    }
    function each()
{
    if (isset($this->_limitRows) && --$this->_limitRows < 0) return false;
    if (key($this->data) === null) return false; // No more elements
    $id = key($this->data);   // Get current key (id)
    $data = current($this->data); // Get current data
    next($this->data);
    return array('id' => $id) + $data;
}
    function end()
    {
        return end($this->data);
    }
    function id_exists($id)
    {
        return isset($this->data[$id]);
    }
    function ids()
    {
        return array_keys($this->data);
    }
    function ksort($sort_flags = 0)
    {
        return ksort($this->data, $sort_flags);
    }
    function krsort($sort_flags = 0)
    {
        return krsort($this->data, $sort_flags);
    }
    function min()
    {
        if (!$this->data) return false;
        return min(array_keys($this->data));
    }
    function max()
    {
        if (!$this->data) return false;
        return max(array_keys($this->data));
    }
    function first()
    {
        if (!$this->data) return false;
        return array_shift(array_keys($this->data));
    }
    function last()
    {
        if (!$this->data) return false;
        end($this->data);
        return key($this->data);
    }

    function prev($id, $offset = 1)
    {
        return $this->next($id, -$offset);
    }
    function next($id, $offset = 1)
    {
        $ids = array_keys($this->data);
        $i = array_search($id, $ids) + $offset;
        return isset($ids[$i]) ? $ids[$i] : false;
    }
    function rand($num_req = 1)
    {
        return empty($this->data) ? false : array_rand($this->data, $num_req);
    }

    function reset()
    {
        return reset($this->data);
    }
    function row_exists($search)
    {
        reset($this->data);
        foreach ($this->data as $id => $row)
        {
            reset($search);
            foreach ($search as $key => $value)
            {
                if (!isset($row[$key]) || $row[$key] != $value) continue 2;
            }
            return true;
        }
        reset($this->data);
        return false;
    }
    private  $_cmpFields = array();
    function sort($sort_flags)
    {
        if (func_num_args() > 1) $sort_flags = func_get_args();
        else $sort_flags = preg_split('/[,\s]+/s', trim($sort_flags));

        $this->_cmpFields = array();
        $p = -1;

        foreach ($sort_flags as $f)
        {
            $f = preg_replace('/^(A|DE)SC$/i', 'SORT_\0', $f);
            if (defined(strtoupper($f))) $f = constant(strtoupper($f));
            if ($f == SORT_ASC)      continue;
            elseif ($f == SORT_DESC) $this->_cmpFields[$p]['order'] = -1;
            elseif (is_int($f))      $this->_cmpFields[$p]['type'] |= $f;
            else
            {
                ++$p;
                $this->_cmpFields[] = array('field' => $f, 'order' => 1, 'type' => 0);
            }
        }

        if (strcasecmp($this->_cmpFields[0]['field'], "id") == 0)
        {
            if ($this->_cmpFields[0]['order'] > 0) ksort($this->data);
            else krsort($this->data);
        }
        else
        {
            uasort($this->data, array(&$this, '_cmp'));
        }

        reset($this->data);
    }
    function _cmp(&$a, &$b)
    {
        foreach ($this->_cmpFields as $f)
        {
            if ($f['type'] & SORT_NULL)
            {
                if (strlen($a[$f['field']]) <= 0 || strlen($b[$f['field']]) <= 0)
                    $f['order'] = -1;
            }

            switch ($f['type'] & ~SORT_NULL)
            {
                case SORT_NUMERIC:
                    $result = ($a[$f['field']] - $b[$f['field']]) * $f['order'];
                    break;
                case SORT_STRING:
                    $result = strcasecmp($a[$f['field']], $b[$f['field']]) * $f['order'];
                    break;
                case SORT_LOCALE_STRING:
                    $result = strcoll(strtolower($a[$f['field']]), strtolower($b[$f['field']])) * $f['order'];
                    break;
                case SORT_NAT:
                    $result = strnatcasecmp($a[$f['field']], $b[$f['field']]) * $f['order'];
                    break;
                case SORT_TIME:
                    $result = (strtotime($a[$f['field']]) - strtotime($b[$f['field']])) * $f['order'];
                    break;
                default:
                    $result = ($a[$f['field']] == $b[$f['field']]) ? 0 :
                        ($a[$f['field']] > $b[$f['field']] ? $f['order'] : -$f['order']);
                    break;
            }
            if ($result != 0)
                return $result;
        }
    }
    function data($id)
    {
        return isset($this->data[$id]) ? array('id' => $id) + $this->data[$id]
            : false;
    }
    function dump()
    {
        echo $this->export();
    }
    function exists()
    {
        return file_exists($this->filename);
    }
    function export()
    {
        $count_fields = count($this->fields);
        $tr_from = array('"',  "\x00");
        $tr_to   = array('""', "\\\x7F");

        $csv = implode($this->delimiter, $this->fields) . "\r\n";
        reset($this->data);
        foreach ($this->data as $id => $row)
        {
            if (strpos($id, $this->delimiter) === false &&
                strpos($id, '"') === false)
            {
                $csv .= $id;
            }
            else
            {
                $csv .= '"' . str_replace('"', '""', $id) . '"';
            }
            for ($c = 1; $c < $count_fields; ++$c)
            {
                $csv .= $this->delimiter;
                $d = $row[$this->fields[$c]];
                if (strlen($d))
                {
                    
                    $d = preg_replace('/\\\(?=\\\|\x00|"|\x7F|\x93|$)/s',
                        '\\\\\0', $d);
                    $d = preg_replace('/(^"|"$)/s', "\\\x93", $d);
                    $csv .= '"' . str_replace($tr_from, $tr_to, $d) . '"';
                }
            }
            $csv .= "\r\n";
        }
        reset($this->data);
        return $csv;
    }
    function is_writeable()
    {
        return is_writeable($this->filename);
    }

  
    function seek($id = 0, $whence = null)
    {
        if (!isset($whence)) $id = array_search($id, array_keys($this->data));
        if ($whence == SEEK_END) $id = count($this->data) - 1 - abs($id);
        if ($whence != SEEK_CUR) reset($this->data);
        for ($i = 0; $i < $id; ++$i)
        {
            if (!next($this->data)) return false;
        }
        return true;
    }

   
    function write($tablename = "", $delimiter = "")
    {
        if ($tablename && !preg_match('/\.\w+$/', $tablename))
        {
            $tablename .= ".csv";
        }
        if ($tablename && $tablename != $this->filename)
        {
            $this->close();
            $this->filename = $tablename;
        }
        if (!$this->filename) return false;
        if (!$this->_fp)
        {
            $this->_fp = fopen($this->filename, "wb");
            if (!$this->_fp) return false;
            flock($this->_fp, LOCK_EX);
        }
        if ($delimiter) $this->delimiter = $delimiter;

        rewind($this->_fp);
        if (!fwrite($this->_fp, $this->export()))
        {
            user_error("MyCSV::write() failed, file $this->filename seems to be read only",
                E_USER_WARNING);
            return false;
        }
        ftruncate($this->_fp, ftell($this->_fp));
        $this->close();
        if (count($this->fields) <= 1 && empty($this->data))
        {
            unlink($this->filename);
        }

        return true;
    }
    function close()
    {
        if ($this->_fp)
        {
            fflush($this->_fp);
            flock($this->_fp, LOCK_UN);
            fclose($this->_fp);
            $this->_fp = false;
            if (isset($GLOBALS['_MyCSV_locked'][$this->filename]))
                unset($GLOBALS['_MyCSV_locked'][$this->filename]);
        }
    }
}
