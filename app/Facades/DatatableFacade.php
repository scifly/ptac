<?php
namespace App\Facades;

use DateTime;
use Carbon\Carbon;
use App\Helpers\Snippet;
use App\Helpers\ModelTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class DatatableFacade extends Facade {

    use ModelTrait;

    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the menu functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     * @param Model $model
     * @param array $columns Column information array
     * @param array $joins
     * @param string $condition
     * @return array Server-side processing response array
     * @internal param array|PDO $conn PDO connection resource or connection parameters array
     * @internal param string $table SQL table to query
     * @internal param string $primaryKey Primary key of the table
     */
    static function simple(Model $model, array $columns, array $joins = null, $condition = null) {
        
        $modelName = class_basename($model);
        $tableName = $model->getTable();
        switch ($modelName) {
            case 'Group':
                $useTable = 'Groups';
                break;
            case 'Order':
                $useTable = 'Orders';
                break;
            case 'Table':
                $useTable = 'Tables';
                break;
            case 'Procedure':
                $useTable = 'Procedures';
                break;
            default:
                $useTable = $modelName;
                break;
        }
        $from = $tableName . ' AS ' . $useTable;
        if (isset($joins)) {
            foreach ($joins as $join) {
                $from .=
                    ' ' . $join['type'] . ' JOIN ' . $join['table'] .
                    ' AS ' . $join['alias'] .
                    ' ON ' . implode(' AND ', $join['conditions']);
            }
        }
        // Build the SQL query string from the request
        $limit = self::limit();
        $order = self::order($columns);
        $where = self::filter($columns);
        if (isset($condition)) {
            $where = empty($where)
                ? ' WHERE ' . $condition
                : $where . ' AND ' . $condition;
        }
        // Main query to actually get the data
        $fields = implode(", ", self::pluck($columns, 'db'));
        $query = "SELECT SQL_CALC_FOUND_ROWS " . $fields . " FROM " . $from . $where . $order . $limit;
        $data = DB::select($query);
        $query = "SELECT " . $useTable . ".id FROM " . $from . $where;
        $ids = DB::select($query);
        $rowIds = [];
        foreach ($ids as $id) {
            $rowIds[] = $id->id;
        }
        // Data set length after filtering
        $resFilterLength = DB::select("SELECT FOUND_ROWS() AS t");
        $recordsFiltered = $resFilterLength[0]->t;
        // Total data set length
        $resTotalLength = DB::select("SELECT COUNT(*) AS t FROM " . $tableName)[0]->t;
        $recordsTotal = $resTotalLength;
        // Output
        return [
            "draw"            => intval(Request::get('draw')),
            "ids"             => $rowIds,
            "recordsTotal"    => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data"            => self::data_output($columns, $data),
        ];
        
    }
    
    /**
     * The difference between this method and the `simple` one, is that you can
     * apply additional `where` conditions to the SQL queries. These can be in
     * one of two forms:
     *
     * * 'Result condition' - This is applied to the result set, but not the
     *   overall paging information query - i.e. it will not effect the number
     *   of records that a user sees they can have access to. This should be
     *   used when you want apply a filtering condition that the user has sent.
     * * 'All condition' - This is applied to all queries that are made and
     *   reduces the number of records that the user can access. This should be
     *   used in conditions where you don't want the user to ever have access to
     *   particular records (for example, restricting by a login id).
     *
     * @param Model $model
     * @param  array $columns Column information array
     * @param  string $whereResult WHERE condition to apply to the result set
     * @param  string $whereAll WHERE condition to apply to all queries
     * @return array Server-side processing response array
     * @internal param Request $request Data sent to server by DataTables
     * @internal param array|PDO $conn PDO connection resource or connection parameters array
     * @internal param string $table SQL table to query
     * @internal param string $primaryKey Primary key of the table
     */
    static function complex(Model $model, $columns, $whereResult = null, $whereAll = null) {
        
        # $localWhereResult = [];
        # $localWhereAll = [];
        $whereAllSql = '';
        $table = $model->getTable();
        // Build the SQL query string from the request
        $limit = self::limit();
        $order = self::order($columns);
        $where = self::filter($columns);
        $whereResult = self::_flatten($whereResult);
        $whereAll = self::_flatten($whereAll);
        if ($whereResult) {
            $where = $where ?
                $where . ' AND ' . $whereResult :
                'WHERE ' . $whereResult;
        }
        if ($whereAll) {
            $where = $where ?
                $where . ' AND ' . $whereAll :
                'WHERE ' . $whereAll;
            $whereAllSql = 'WHERE ' . $whereAll;
        }
        // Main query to actually get the data
        $data = DB::select(
            "SELECT SQL_CALC_FOUND_ROWS `" .
            implode("`, `", self::pluck($columns, 'db')) .
            "` FROM " . $table . $where . $order . $limit
        );
        // Data set length after filtering
        $resFilterLength = DB::select("SELECT FOUND_ROWS() AS cnt");
        $recordsFiltered = $resFilterLength[0]->cnt;
        // Total data set length
        $resTotalLength = DB::select("SELECT COUNT(*) AS cnt FROM " . $table . $whereAllSql);
        $recordsTotal = $resTotalLength[0]->cnt;
        
        /* Output */
        return [
            "draw"            => intval(Request::get('draw')),
            "recordsTotal"    => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data"            => self::data_output($columns, $data),
        ];
    }

    /**
     * Display data entry operations
     *
     * @param $active
     * @param $row
     * @param bool $show
     * @param bool $edit
     * @param bool|true $del - if set to false, do not show delete link
     * @return string
     */
    static function dtOps($active, $row, $show = true, $edit = true, $del = true) {

        $user = Auth::user();
        $id = $row['id'];
        $showLink = sprintf(Snippet::DT_LINK_SHOW, 'show_' . $id);
        $editLink = sprintf(Snippet::DT_LINK_EDIT, 'edit_' . $id);
        $delLink = sprintf(Snippet::DT_LINK_DEL, $id);
        
        return
            Snippet::status($active) .
            ($show ? ($user->can('act', self::uris()['show']) ? $showLink : '') : '') .
            ($edit ? ($user->can('act', self::uris()['edit']) ? $editLink : '') : '') .
            ($del ? ($user->can('act', self::uris()['destroy']) ? $delLink : '') : '');
        
    }
    
    /**
     * Create the data output array for the DataTables rows
     *
     * @param  array $columns Column information array
     * @param  array $data Data from the SQL get
     * @return array Formatted data in a row based format
     */
    static function data_output(array $columns, array $data) {
        
        $out = [];
        $length = count($data);
        for ($i = 0; $i < $length; $i++) {
            $row = [];
            $_data = (array)$data[$i];
            $j = 0;
            foreach ($_data as $name => $value) {
                if (isset($value) && self::validateDate($value) && $name != 'birthday' && $name != 'punch_time') {
                    Carbon::setLocale('zh');
                    $dt = Carbon::createFromFormat('Y-m-d H:i:s', $value);
                    $value = $dt->diffForhumans();
                }
                $column = $columns[$j];
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = $column['formatter']($value, $_data);
                } else {
                    $row[$column['dt']] = $value;
                }
                $j++;
            }
            $out[] = $row;
        }
        
        return $out;
        
    }
    
    /**
     * Return a string from an array or a string
     *
     * @param  array|string $a Array to join
     * @param  string $join Glue for the concatenation
     * @return string Joined string
     */
    static function _flatten($a, $join = ' AND ') {
        
        if (!$a) {
            return '';
        } else if ($a && is_array($a)) {
            return implode($join, $a);
        }
        
        return $a;
        
    }
    
    private static function validateDate($date, $format = 'Y-m-d H:i:s') {
        
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
        
    }
    
    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     * @return string SQL limit clause
     * @internal param Request $request Data sent to server by DataTables
     * @internal param array $columns Column information array
     */
    private static function limit() {
        
        $limit = '';
        $start = Request::get('start');
        $length = Request::get('length');
        if (isset($start) && $length != -1) {
            $limit = "LIMIT " . intval($start) . ", " . intval($length);
        }
        
        return $limit;
        
    }
    
    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     * @param array $columns Column information array
     * @return string SQL order by clause
     * @internal param Request $request Data sent to server by DataTables
     */
    private static function order(array $columns) {
        
        $orderBy = '';
        $order = Request::get('order');
        if (isset($order) && count($order)) {
            $orderBy = [];
            $dtColumns = self::pluck($columns, 'dt');
            for ($i = 0, $ien = count($order); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($order[$i]['column']);
                $requestColumn = Request::get('columns')[$columnIdx];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['orderable'] == 'true') {
                    $dir = $order[$i]['dir'] === 'asc' ? ' ASC ' : ' DESC ';
                    $pos = stripos($column['db'], ' as ');
                    if ($pos) {
                        $column['db'] = substr($column['db'], 0, $pos);
                    }
                    $orderBy[] = /*'`' . */
                        $column['db'] . ' ' . $dir;
                }
            }
            $orderBy = ' ORDER BY ' . implode(', ', $orderBy);
        }
        
        return $orderBy;
        
    }
    
    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     * @param array $a Array to get data from
     * @param string $prop Property to read
     * @return array        Array of property values
     */
    private static function pluck(array $a, $prop) {
        
        $out = [];
        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = $a[$i][$prop];
        }
        
        return $out;
        
    }
    
    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     * @param array $columns Column information array
     * @return string SQL where clause
     * @internal param Request $request Data sent to server by DataTables
     * @internal param array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     */
    private static function filter(array $columns) {
        
        $globalSearch = [];
        $columnSearch = [];
        $dtColumns = self::pluck($columns, 'dt');
        $requestSearch = Request::get('search');
        $requestColumns = Request::get('columns');
        if (isset($requestSearch) && $requestSearch['value'] != '') {
            $str = $requestSearch['value'];
            $keys = explode(' ', $str);
            for ($j = 0; $j < count($keys); $j++) {
                for ($i = 0, $ien = count($requestColumns); $i < $ien; $i++) {
                    $requestColumn = $requestColumns[$i];
                    $columnIdx = array_search($requestColumn['data'], $dtColumns);
                    $column = $columns[$columnIdx];
                    if ($requestColumn['searchable'] == 'true') {
                        # $binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                        $pos = stripos($column['db'], ' as ');
                        if ($pos) {
                            $column['db'] = substr($column['db'], 0, $pos);
                        }
                        $globalSearch[$j][] = $column['db'] . " LIKE BINARY '%" . $keys[$j] . "%'";
                    }
                }
            }
        }
        // Individual column filtering
        for ($i = 0, $ien = count($requestColumns); $i < $ien; $i++) {
            $requestColumn = $requestColumns[$i];
            $columnIdx = array_search($requestColumn['data'], $dtColumns);
            $column = $columns[$columnIdx];
            $str = $requestColumn['search']['value'];
            if ($requestColumn['searchable'] == 'true' && $str != '') {
                # $binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                $columnSearch[] = $column['db'] . " LIKE BINARY '%" . $str . "%'";
            }
        }
        // Combine the filters into a single string
        $where = '';
        $filters = [];
        if (count($globalSearch)) {
            for ($i = 0; $i < count($globalSearch); $i++) {
                $filters[$i] = '(' . implode(' OR ', $globalSearch[$i]) . ')';
            }
            $where = '(' . implode(' AND ', $filters) . ')';
        }
        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where . ' AND ' . implode(' AND ', $columnSearch);
        }
        if ($where !== '') {
            $where = ' WHERE ' . $where;
        }
        
        return $where;
        
    }
    
    protected static function getFacadeAccessor() { return 'Datatable'; }
    
}