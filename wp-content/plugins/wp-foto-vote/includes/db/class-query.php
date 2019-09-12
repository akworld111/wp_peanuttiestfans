<?php

//namespace WordPress\ORM;
//https://github.com/doctrine/dbal/blob/master/lib/Doctrine/DBAL/Query/QueryBuilder.php

defined('ABSPATH') or die("No script kiddies please!");

/*
 * Abstract class, represents classik actions with database, use WordPress functions
 * http://habrahabr.ru/post/154245/
 */

if (class_exists('FvModel')) {
    return;
}

class FvModel extends FvQuery
{
}

/**
 * Progressively build up a query to get results using an easy to understand DSL.
 *
 * @author Brandon Wamboldt <brandon.wamboldt@gmail.com>
 */
class FvQuery
{

    /**
     * @var string
     */
    const ORDER_ASCENDING = 'ASC';

    /**
     * @var string
     */
    const ORDER_DESCENDING = 'DESC';

    /**
     * @var string
     */
    protected $what_field = "";

    /**
     * @var string
     */
    protected $what_fields = array();

    /**
     * @var integer
     */
    protected $limit = 0;

    /**
     * @var integer
     */
    protected $offset = 0;

    /**
     * @var array
     */
    protected $where = array();

    /**
     * @var string
     */
    protected $sort_by = array();
    //'id' => 'ASC'

    /**
     * @var string
     */
    //protected $order = ;

    /**
     * @var string
     */
    protected $group = '';

    /**
     * @var array
     */
    protected $join = array();

    /**
     * @var string|null
     */
    protected $search_term = null;

    /**
     * @var array
     */
    protected $search_fields = array();

    /**
     * @var string
     */
    protected $model;

    /**
     * @var string
     */
    protected $primary_key;

    public function __construct()
    {
        $this->primary_key = 'id';
        //$this->query = $query;
    }

    /**
     * Return the string representation of the query.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->compose_query();
    }

    /* ===========================================
     * PUBLIC ACTION FUNCTIONS
     ============================================ */

    /**
     * Compose & execute our query.
     * Can be user for retrieve only one row
     *
     * @return FV_Abstract_Object row
     */
    public function findRow()
    {
        global $wpdb;

        $result = $wpdb->get_row($this->compose_query(false), OBJECT);
        return $result;
    }

    /**
     * Compose & execute our query.
     *
     * @param  boolean  $only_count     Whether to only return the row count
     * @param  boolean  $get_var
     * @param  string   $res_type       Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
     *
     * @return array
     */
    public function find($only_count = false, $get_var = false, $res_type = OBJECT)
    {
        global $wpdb;

        //$query = $this->query;
        // Query
        if ($only_count) {
            return (int)$wpdb->get_var($this->compose_query(true));
        } elseif ($get_var) {
            return $wpdb->get_var($this->compose_query(true));
        }

        $results = $wpdb->get_results($this->compose_query(false), $res_type);
        /*
          if ($results) {
          foreach ($results as $index => $result) {
          $results[$index] = $query::create((array) $result);
          }
          }
         */
        $this->checkDbErrors();
        return $results;
    }

    /**
     * Return queried variable
     * @uses "$wpdb->get_var()"
     *
     * @return string
     */
    public function findVar()
    {
        global $wpdb;

        //$query = $this->query;
        // Query
        return $wpdb->get_var($this->compose_query(false));
    }


    /**
     * find one record with All fields in table by Primary KEY
     * @since     1.0.0
     *
     * @param   int     $id
     * @param   bool    $from_cache
     * @param   string  $res_type Optional.     Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
     * @return  object|array
     */
    public function findByPK($id, $from_cache = false, $res_type = OBJECT)
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT * FROM " . $this->tableName() . " WHERE `" . $this->primary_key . "` = %d; ", $id
        );

        if (!$from_cache || !$r = wp_cache_get($this->tableName() . '-findByPK-' . $id, 'fv')) {
            $r = $wpdb->get_row($sql, $res_type);
        }
        $this->checkDbErrors();
        wp_cache_add($this->tableName() . '-findByPK-' . $id, $r, 'fv');

        return $r;
    }

    /**
     * Delete record from table by PRIMARY KEY
     *
     * @since     1.0.0
     *
     * @example ModelCompetitors::q()->delete(2);
     *
     * @param   $id    int
     * @return  bool    MySQL query result
     */
    public function delete($id)
    {
        global $wpdb;
        
        $r = $wpdb->delete( $this->tableName(), array( $this->primary_key => $id ), array( '%d' ) );

        wp_cache_delete($this->tableName() . '-findByPK-' . $id, 'fv');

        $this->checkDbErrors();
        return $r;
    }

    /**
     * Insert record into table using Wordpress Insert Function and return ID
     * @since     1.0.0
     *
     * @example
     * ModelCompetitors::q()->insert(array(
     *  'name'         => 'test insert',
     *  'description'  => 'test description',
     *  'image_id'     => 22,
     * ));
     *
     * @param   array      $data
     * @return  int        Last inserted ID
     */
    public function insert($data)
    {
        global $wpdb;
        // Format for database (string, int)
        $sql_format = array();
        // Array - data to save
        $sql_data = array();
        $fields = $this->fields();
        foreach ($data as $key => $value) {
            if (isset($fields[$key])) {
                $sql_data[$key] = $value;
                $sql_format[] = $fields[$key];
            }
        }
        // do Query
        $wpdb->insert(
            $this->tableName(), $sql_data, $sql_format
        );
        $this->checkDbErrors();
        return $wpdb->insert_id;
    }

    /**
     * Update record by simple condition using Wordpress Update Function
     * Comdition may be array, int or false
     * <code>
     * Example 1:
     * TestModel::query()->update(
     *        array('name'=>'Test'),                // DATA
     *        array( 'contest_id' =>1)              // Condition
     * );
     * </code>
     * @since     1.0.0
     * @param array $data
     * @param mixed $condition Record ID or
     *
     * @return bool MySQL query result
     */
    public function update($data, $condition = false)
    {
        global $wpdb;
        // Format for database (string, int)
        $sql_format = array();
        // Array - data to save
        $sql_data = array();
        $fields = $this->fields();
        foreach ($data as $key => $value) {
            if (isset($fields[$key])) {
                $sql_data[$key] = $value;
                $sql_format[] = $fields[$key];
            }
        }
        // may be primary key not set in fileds due to secure it for random change
        $fields[$this->primary_key] = '%d';
        // Format for database (string, int)
        $condition_format = array();
        // Array - data to save
        $condition_data = array();
        if (is_array($condition)) {
            foreach ($condition as $key => $value) {
                if (isset($fields[$key])) {
                    $condition_data[$key] = $value;
                    $condition_format[] = $fields[$key];
                }
            }
        } elseif (is_numeric($condition)) {
            $condition_data = array($this->primary_key => (int)$condition);
            $condition_format = array('%d');
        } elseif ($condition === false && isset($data[$this->primary_key])) {
            $condition_data = array($this->primary_key => (int)$data[$this->primary_key]);
            $condition_format = array('%d');
        }
        // do Query
        $r = $wpdb->update(
            $this->tableName(), $sql_data, $condition_data, $sql_format, $condition_format
        );
        $this->checkDbErrors();
        return $r;
    }


    /**
     * Update record by Primary KEY using Wordpress Update Function
     * <code>
     * Example 1:
     * ModelCompetitors::query()->updateByPK(
     *        array('name'=>'Test'),                // DATA
     *        10        // PK ID
     * );
     *
     * @since     1.0.0
     * @param array $data
     * @param int $pkID Record ID
     *
     * @return bool MySQL query result
     */
    public function updateByPK($data, $pkID)
    {
        global $wpdb;
        // Format for database (string, int)
        $sql_format = array();
        // Array - data to save
        $sql_data = array();
        $fields = $this->fields();

        foreach ($data as $key => $value) {
            if (isset($fields[$key])) {
                $sql_data[$key] = $value;
                $sql_format[] = $fields[$key];
            }
        }
        // may be primary key not set in fileds due to secure it for random change
        $fields[$this->primary_key] = '%d';

        $condition_data = array($this->primary_key => (int)$pkID);
        // do Query
        $r = $wpdb->update(
            $this->tableName(), $sql_data, $condition_data, $sql_format, array('%d')
        );
        $this->checkDbErrors();

        // Let's clear Cache
        wp_cache_delete($this->tableName() . '-findByPK-' . $pkID, 'fv');

        return $r;
    }

    /**
     * Fetch all rows with $default params
     * If nothing found - create new, else update one parameter for exists
     *
     * @param $key          string
     * @param $value        mixed
     * @param $default      array       Data for Insert
     *
     * @return bool
     */
    public function updateOrInsert($key, $value, $default)
    {
        $fields = $this->fields();
        if (!isset($fields[$key])) {
            trigger_error($key . ' is not specified in Fields list [WP Foto Vote]!');
            return false;
        }

        global $wpdb;

        $row = $this->where_all($default)->findRow();

        // Row Exists
        if ($row) {
            $where = $this->compose_where_sql();

            if (!empty($where)) {
                $where = ' WHERE ' . $where;
            }

            $r = $wpdb->query('UPDATE `' . $this->tableName() . '` as t SET `' . $key . '` = \'' . $value . '\' ' . $where . ';');
        } else {
            // Create Record
            $default[$key] = $value;
            $r = $this->insert($default);
        }

        FvLogger::checkDbErrors($r);

        return $r;
    }


    /* ===========================================
     * END PUBLIC ACTION FUNCTIONS
     ============================================ */

    /**
     * Reset one field, can used for change Sort / Where
     *
     * @param  string $param
     * @return self self
     */
    public function resetParam($param)
    {
        switch ($param) {
            case 'sort_by':
                $this->sort_by = array();
                break;
            case 'where':
                $this->where = array();
                break;
            case 'join':
                $this->join = array();
                break;
            case 'search_term':
                $this->search_term = '';
                $this->search_fields = array();
                break;
        }
        return $this;
    }

    /**
     * Set the fields to include in the search.
     *
     * @param  array $fields
     * @return self
     */
    public function set_searchable_fields(array $fields)
    {
        $this->search_fields = $fields;

        return $this;
    }

    /**
     * Set the primary key column.
     *
     * @param string $primary_key
     * @return self
     */
    public function set_primary_key($primary_key)
    {
        $this->primary_key = $primary_key;
        $this->sort_by[$primary_key] = self::ORDER_ASCENDING;

        return $this;
    }

    /**
     * Set field to select
     *
     * @param  string $field Ex.: SUM(id)
     * @return self self
     * @deprecated
     */
    public function what_field($field)
    {
        $this->what_field = sanitize_text_field($field);
        return $this;
    }

    /**
     * Set field to select from database
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->what_fields( array('id', 'name', 'contest_id') )
     *      ->limit( 5 )
     *      ->find();
     * </code>
     *
     * @param  array $fields Ex.: ["id", "name"]
     * @param  bool  $join
     * @return self
     */
    public function what_fields($fields, $join = false)
    {
        /* @since 2.2.607 */
        // ## Fix to avoid issues with empty results form main table
        if ($join && !$this->what_fields) {
            $this->what_fields = array('`t`.*');
        }
        $this->what_fields = array_merge($this->what_fields, (array)$fields);
        return $this;
    }

    /**
     * Set the maximum number of results to return at once.
     *
     * <code>
     *  ModelCompetitors::q()->limit( 5 )->find();
     * </code>
     *
     * @param  integer $limit
     * @return self self
     */
    public function limit($limit)
    {
        $this->limit = (int)$limit;

        return $this;
    }

    /**
     * Set the offset to use when calculating results.
     *
     * @param  integer $offset
     * @return self self
     */
    public function offset($offset)
    {
        $this->offset = (int)$offset;

        return $this;
    }

    /**
     * Set the column we should sort by.
     *
     * @param  string $sort_by_field
     * @param  string $order
     * @return self self
     */
    public function sort_by($sort_by_field, $order = 'ASC')
    {
        if (strlen($sort_by_field) > 1) {
            if ($order != self::ORDER_ASCENDING && $order != self::ORDER_DESCENDING) {
                $order = self::ORDER_ASCENDING;
            }

            $this->sort_by[$sort_by_field] = $order;
        }

        return $this;
    }

    /**
     * Set the order we should sort by.
     *
     * @param  string $order
     * @return self self
     * @deprecated since 2.2.123
     */
    public function order($order)
    {
        trigger_error('This function is Deprecated since version 2.2.123. Use "sort_by($sort_by, $order)" with second parameter.', E_USER_NOTICE);
        //$this->order = $order;
        return $this;
    }

    /**
     * Set the group we should group by.
     *
     * @param  string $group
     * @return self self
     */
    public function group_by($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Add a `=` clause to the search query.
     *
     * @example ->where('image_id', 299)
     *
     * @param  string $column
     * @param  string $value
     * @return self
     */
    public function where($column, $value)
    {
        $this->where[] = array('type' => 'where', 'column' => $column, 'value' => $value);

        return $this;
    }

    /**
     * Add a custom WHERE clause to the search query.
     *
     *
     *
     * @param  string $column
     * @param  string $value
     * @return self self
     */
    public function where_custom($column, $value)
    {
        $this->where[] = array('type' => 'custom', 'column' => $column, 'value' => $value);

        return $this;
    }

    /**
     * Add a custom WHERE clause to the search query.
     *
     * @example ->where_custom_sql('image_id IS NOT NULL')
     *
     * @param  string $sql
     * @return self self
     */
    public function where_custom_sql($sql)
    {
        $this->where[] = array('type' => 'custom_sql', 'sql' => $sql);

        return $this;
    }

    /**
     * Add a `!=` clause to the search query.
     *
     * @example ->where_not('image_id', 50)
     *
     * @param  string $column
     * @param  string $value
     * @return self self
     */
    public function where_not($column, $value)
    {
        $this->where[] = array('type' => 'not', 'column' => $column, 'value' => $value);

        return $this;
    }

    /**
     * Add IS NULL clause to the search query.
     *
     * @example ->where_null('image_id')
     *
     * @param  string $column
     * @return self self
     */
    public function where_null($column)
    {
        $this->where[] = array('type' => 'null', 'column' => $column);

        return $this;
    }

    /**
     * Add IS NOT NULL clause to the search query.
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->where_not_null('image_id')
     *      ->limit( 5 )
     *      ->find();
     * </code>
     *
     * @param  string $column
     * @return self self
     */
    public function where_not_null($column)
    {
        $this->where[] = array('type' => 'not_null', 'column' => $column);

        return $this;
    }

    /**
     * Add a `LIKE` clause to the search query.
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->where_like('name', "search text")
     *      ->limit( 5 )
     *      ->find();
     * </code>
     *
     * @param  string $column
     * @param  string $value
     * @return self self
     */
    public function where_like($column, $value)
    {
        $this->where[] = array('type' => 'like', 'column' => $column, 'value' => $value);

        return $this;
    }

    /**
     * Add a `NOT LIKE` clause to the search query.
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->where_not_like('name', "no search text")
     *      ->limit( 5 )
     *      ->find();
     * </code>
     *
     * @param  string $column
     * @param  string $value
     * @return self self
     */
    public function where_not_like($column, $value)
    {
        $this->where[] = array('type' => 'not_like', 'column' => $column, 'value' => $value);

        return $this;
    }

    /**
     * Add a `<` clause to the search query.
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->where_lt('name', 50)
     *      ->limit( 5 )
     *      ->find();
     * </code>
     *
     * @param  string $column
     * @param  string $value
     * @return self self
     */
    public function where_lt($column, $value)
    {
        $this->where[] = array('type' => 'lt', 'column' => $column, 'value' => $value);

        return $this;
    }

    /**
     * Add a `<=` clause to the search query.
     *
     * @param  string $column
     * @param  string $value
     * @return self self
     */
    public function where_lte($column, $value)
    {
        $this->where[] = array('type' => 'lte', 'column' => $column, 'value' => $value);

        return $this;
    }

    /**
     * Add a `>` clause to the search query.
     *
     *
     * @param  string $column
     * @param  string $value
     * @return self self
     */
    public function where_gt($column, $value)
    {
        $this->where[] = array('type' => 'gt', 'column' => $column, 'value' => $value);

        return $this;
    }

    /**
     * Add a `>=` clause to the search query.
     *
     * @param  string $column
     * @param  string $value
     * @return self self
     */
    public function where_gte($column, $value)
    {
        $this->where[] = array('type' => 'gte', 'column' => $column, 'value' => $value);

        return $this;
    }

    /**
     * Add an `IN` clause to the search query.
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->where_in( 'id', array(10,11,12,13) )
     *      ->limit( 5 )
     *      ->find();
     * </code>
     *
     * @param  string $column
     * @param  array $in
     * @return self self
     */
    public function where_in($column, array $in)
    {
        $this->where[] = array('type' => 'in', 'column' => $column, 'value' => $in);

        return $this;
    }

    /**
     * Add a `NOT IN` clause to the search query.
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->where_not_in( 'id', array(10,11,12,13) )
     *      ->limit( 5 )
     *      ->find();
     * </code>
     *
     * @param  string $column
     * @param  array $not_in
     * @return self self
     */
    public function where_not_in($column, array $not_in)
    {
        $this->where[] = array('type' => 'not_in', 'column' => $column, 'value' => $not_in);

        return $this;
    }

    /**
     * Add an OR statement to the where clause (e.g. (var = foo OR var = bar OR
     * var = baz)).
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->where_any( array('id'=>1, 'name'=>'test') )
     *      ->limit( 5 )
     *      ->find();
     * </code>
     *
     * @param  array $where
     * @return self self
     */
    public function where_any(array $where)
    {
        $this->where[] = array('type' => 'any', 'where' => $where);

        return $this;
    }

    /**
     * Add an AND statement to the where clause (e.g. (var1 = foo AND var2 = bar
     * AND var3 = baz)).
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->where_all( array('id'=>1, 'name'=>'test') )
     *      ->limit( 5 )
     *      ->find();
     * </code>
     *
     * @param  array $where
     * @return self self
     */
    public function where_all(array $where)
    {
        $this->where[] = array('type' => 'all', 'where' => $where);

        return $this;
    }

    /**
     * Add an AND statement to the where clause
     * date(field) >= date(param)
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->where_later( 'date_start', time() )
     *      ->limit( 5 )
     *      ->find();
     * </code>
     *
     * @param  string $column
     * @param  timestamp $value
     * @return self
     */
    public function where_later($column, $value)
    {
        $this->where[] = array('type' => 'later', 'column' => $column, 'value' => $value);

        return $this;
    }

    /**
     * Add an AND statement to the where clause
     * date(field) <= date(param)
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->where_early( 'date_start', time() )
     *      ->limit( 5 )
     *      ->find();
     * </code>
     *
     * @param  string $column
     * @param  string $value
     * @return self self
     */
    public function where_early($column, $value)
    {
        $this->where[] = array('type' => 'early', 'column' => $column, 'value' => $value);

        return $this;
    }

    /**
     * Get models where any of the designated fields match the given value.
     *
     * @param  string $search_term
     * @return self self
     */
    public function search($search_term)
    {
        $this->search_term = $search_term;

        return $this;
    }

    /**
     * Runs the same query as find, but with no limit and don't retrieve the
     * results, just the total items found.
     *
     * @return integer
     */
    public function total_count()
    {
        return $this->find(true);
    }

    /**
     * Creates and adds a left join to the query.
     *
     * <code>
     *  ModelCompetitors::q()
     *      ->limit( 5 )
     *      ->leftJoin('wp_fv_contest', 'c', 'c.id = t.contest_id')
     *      ->what_fields( array('t.*', 'c.name') )
     *      ->find();
     *
     * </code>
     *
     * @param string $join_table The table name to join.
     * @param string $alias The alias of the join table.
     * @param string $condition The condition for the join.
     * @param array $fields How fields take from join table
     * @param array $where Where condition
     *
     * @return self     the static query class
     */
    public function leftJoin($join_table, $alias, $condition, $fields = array(), $where = '')
    {
        return $this->join('left', $join_table, $alias, $condition, $fields, $where);
    }

    /**
     * Creates and adds a join to the query.
     *
     * <code>
     *   ->join('wp_fv_votes', 'v', 'v.post_id = t.id');
     * </code>
     *
     * @param string $join_type Join type ('LEFT OUTER JOIN', 'RIGHT JOIN', etc.)
     * @param string $join_table The table name to join.
     * @param string $alias The alias of the join table.
     * @param string $condition The condition for the join.
     * @param array $fields How fields take from join table
     * @param bool|array $where Where condition
     *
     * @return self the static query class
     */
    public function join($join_type, $join_table, $alias, $condition, $fields = array(), $where = false)
    {
        $join_hash = md5($join_type.$join_table.$condition);

        // If there no the same Join's
        if ( !isset($this->join[ $join_hash ]) ) {
            $this->join[ $join_hash ] = array(
                'joinType' => $join_type,
                'joinTable' => $join_table,
                'joinAlias' => $alias,
                'joinCondition' => $condition,
                'joinWhere' => $where,
            );
        }

        if ( $fields && is_array($fields) ){
            $what_arr = array();
            foreach ($fields as $key => $field) {
                if (strstr($field, '(') !== false && strstr($field, ')') !== false) {
                    $what_arr[] = $field . " as {$alias}_". sanitize_title_for_query($key);
                } else {
                    $what_arr[] = $alias . ".`" . $field . "` as {$alias}_{$field}";
                }

                $this->what_fields($what_arr, true);
            }
        }

        return $this;
    }
    
    /**
     * Compose the actual SQL WHERE query
     * @return string
     */
    public function compose_where_sql()
    {
        $where = '';
        $where_arr = array();
        $fields = $this->fields();

        // Search
        if (!empty($this->search_term)) {
            $where .= ' (';

            foreach ($this->search_fields as $field) {
                $where .= '`t`.`' . $field . '` LIKE "%' . esc_sql($this->search_term) . '%" OR ';
            }

            $where = substr($where, 0, -4) . ')';

            $where_arr[] = $where;
            $where = '';
        }

        // Where

        foreach ($this->where as $q) {
            if (isset($q['column']) && !isset($fields[$q['column']])) {
                continue;
            }
            // where


            if ($q['type'] == 'where') {
                $where .= ' `t`.`' . $q['column'] . '` = "' . esc_sql($q['value']) . '"';
            } elseif ($q['type'] == 'custom_sql') {
                $where .= $q['sql'];
            } elseif ($q['type'] == 'custom') {
                $where .= ' `t`.`' . $q['column'] . '` ' . $q['value'];
            } elseif ($q['type'] == 'null') {   // where_null
                $where .= ' `t`.`' . $q['column'] . '` IS NULL';
            } elseif ($q['type'] == 'not_null') {
                $where .= ' `t`.`' . $q['column'] . '` IS NOT NULL';
            } // where_not
            elseif ($q['type'] == 'not') {
                $where .= ' `t`.`' . $q['column'] . '` != "' . esc_sql($q['value']) . '"';
            } // where_like
            elseif ($q['type'] == 'like') {
                $where .= ' `t`.`' . $q['column'] . '` LIKE "%' . esc_sql($q['value']) . '%"';
            } // where_not_like
            elseif ($q['type'] == 'not_like') {
                $where .= ' `t`.`' . $q['column'] . '` NOT LIKE "' . esc_sql($q['value']) . '"';
            } // where_lt
            elseif ($q['type'] == 'lt') {
                $where .= ' `t`.`' . $q['column'] . '` < "' . esc_sql($q['value']) . '"';
            } // where_lte
            elseif ($q['type'] == 'lte') {
                $where .= ' `t`.`' . $q['column'] . '` <= "' . esc_sql($q['value']) . '"';
            } // where_gt
            elseif ($q['type'] == 'gt') {
                $where .= ' `t`.`' . $q['column'] . '` > "' . esc_sql($q['value']) . '"';
            } // where_gte
            elseif ($q['type'] == 'gte') {
                $where .= ' `t`.`' . $q['column'] . '` >= "' . esc_sql($q['value']) . '"';
            } // where_early
            elseif ($q['type'] == 'early') {
                $date = date("Y-m-d H:i:s", (int)$q['value']);
                $where .= ' `t`.`' . $q['column'] . '` <= "' . esc_sql($date) . '"';
            } // where_later
            elseif ($q['type'] == 'later') {
                $date = date("Y-m-d H:i:s", (int)$q['value']);
                $where .= ' `t`.`' . $q['column'] . '` >= "' . esc_sql($date) . '"';
            } // where_in
            elseif ($q['type'] == 'in') {
                $where .= ' `t`.`' . $q['column'] . '` IN (';

                foreach ($q['value'] as $value) {
                    $where .= '"' . esc_sql($value) . '",';
                }

                $where = substr($where, 0, -1) . ')';
            } // where_not_in
            elseif ($q['type'] == 'not_in') {
                $where .= ' `t`.`' . $q['column'] . '` NOT IN (';

                foreach ($q['value'] as $value) {
                    $where .= '"' . esc_sql($value) . '",';
                }

                $where = substr($where, 0, -1) . ')';
            } // where_any
            elseif ($q['type'] == 'any') {
                $where .= ' (';

                foreach ($q['where'] as $column => $value) {
                    if (!is_array($value)) {
                        $where .= '`t`.`' . $column . '` = "' . esc_sql($value) . '" OR ';
                    } else {
                        foreach ($value as $column2 => $value2) :
                            $where .= '`t`.`' . $column2 . '` = "' . esc_sql($value2) . '" OR ';
                        endforeach;
                        //FvFunctions::dump( 'before 1: ' . $where);
                        //$where = substr($where, 0, -5) . '")';
                        //FvFunctions::dump('after 1: ' . $where);
                    }
                }
                //FvFunctions::dump( 'before : ' . $where);
                $where = substr($where, 0, -5) . '")';
                //FvFunctions::dump( 'final 2: ' . $where);
            } // where_all
            elseif ($q['type'] == 'all') {
                $where .= ' (';

                foreach ($q['where'] as $column => $value) {
                    $where .= '`t`.`' . $column . '` = "' . esc_sql($value) . '" AND ';
                }

                $where = substr($where, 0, -5) . ')';
            }

            $where_arr[] = $where;
            $where = '';
            // Finish where clause
        }

        return implode(' AND ', $where_arr);
    }

    /**
     * Compose the actual SQL query from all of our filters and options.
     *
     * @param  boolean $only_count Whether to only return the row count
     * @param  boolean $get_var Whether to only return the variable
     * @return string
     */
    public function compose_query($only_count = false)
    {
        //$query  = $this->query;
        $table = $this->tableName();
        $where = '';
        $group = '';
        $order = '';
        $limit = '';
        $offset = '';
        $fields = $this->fields();

        $where = $this->compose_where_sql();

        if (!$only_count) {
            // group
            if (!empty($this->group)) {
                $group = ' GROUP BY ' . $this->group;
            }

            if (!empty($this->sort_by) && is_array($this->sort_by)) {
                $order_arr = array();
                foreach ($this->sort_by as $sort_field => $sort_order) {
                    // Order
                    if (strstr($sort_field, '(') !== false && strstr($sort_field, ')') !== false) {
                        // The sort column contains () so we assume its a function, therefore
                        // don't quote it
                        $order_arr[] = $sort_field . ' ' . $sort_order;
                    } elseif (isset($fields[$sort_field])) {
                        $order_arr[] = '`t`.`' . $sort_field . '` ' . $sort_order;
                    } else {
                        $order_arr[] = $sort_field . ' ' . $sort_order;
                    }
                }
                $order = ' ORDER BY ' . implode(', ', $order_arr);
                unset($order_arr);
            }

            // Limit
            if ($this->limit > 0) {
                $limit = ' LIMIT ' . (int)$this->limit;
            }

            // Offset
            if ($this->offset > 0) {
                $offset = ' OFFSET ' . (int)$this->offset;
            }

        }

        $what = '';
        if (!empty($this->what_field)) {
            $this->what_fields[] = $this->what_field;
        }
        if (!empty($this->what_fields)) {
            $what .= implode(',', array_unique($this->what_fields));
        }
        if ( !$what ) {
            $what = " `t`.* ";
        }

        $join_sql = "";
        if (!empty($this->join)) {
            foreach ($this->join as $join) {
                $join_sql .= ' ' . strtoupper($join['joinType'])
                    . ' JOIN `' . $join['joinTable'] . '` ' . $join['joinAlias']
                    . ' ON ' . ((string)$join['joinCondition']);

                if (!empty($join['joinWhere'])) {
                    $where .= ' AND ' . $join['joinWhere'];
                }
            }
        }

        // Remove " AND "
        if (!empty($where)) {
            $where = ' WHERE ' . $where;
        }

        // Query
        if ($only_count) {
            $qurey_res = apply_filters('wporm_count_query', "SELECT COUNT(*) FROM `{$table}` t {$join_sql} {$where};", $table);
        } else {
            $qurey_res = apply_filters('wporm_query', "SELECT {$what} FROM `{$table}` t {$join_sql} {$where}{$group}{$order}{$limit}{$offset};", $table);
        }

        return $qurey_res;
    }

    /**
     * check errors, and record into file
     * @since     1.0.0
     * @return  void
     */
    protected function checkDbErrors()
    {
        FvLogger::checkDbErrors();
    }

}

/*
add_filter('wporm_query', 'fv_orm_log_queries', 10, 2);

function fv_orm_log_queries($sql, $model_class) {
	echo '<pre>' . $sql . '</pre>';
	return $sql;
};
*/