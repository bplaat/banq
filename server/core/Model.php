<?php

// The framework abstract model class
abstract class Model {
    // The standart primary key field is the id field
    protected static $primaryKey = 'id';

    // A function whichs returns the table name
    public static function table () {
        return isset(static::$table) ? static::$table : strtolower(static::class);
    }

    // A function whichs returns the primary key field name of the model
    public static function primaryKey () {
        return static::$primaryKey;
    }

    // A model must have an create function
    abstract public static function create ();

    // A query function thats clears the table
    public static function clear () {
        return Database::query('DELETE FROM `' . static::table() . '`');
    }

    // A query function thats drops / deletes the table
    public static function drop () {
        return Database::query('DROP TABLE IF EXISTS `' . static::table() . '`');
    }

    // A query function that select rows from a table
    public static function select ($where = null) {
        if (is_null($where)) {
            return Database::query('SELECT * FROM `' . static::table() . '`');
        } else {
            if (!is_array($where)) $where = [ static::$primaryKey => $where ];
            foreach ($where as $key => $value) $wheres[] = '`' . $key . '` = ?';
            return Database::query('SELECT * FROM `' . static::table() . '` WHERE ' . implode(' AND ', $wheres), ...array_values($where));
        }
    }

    // A query function that selects rows from a table by page
    public static function selectPage ($page, $per_page) {
        return Database::query('SELECT * FROM `' . static::table() . '` ORDER BY `created_at` DESC LIMIT ?, ?', ($page - 1) * $per_page, $per_page);
    }

    // A query function that inserts rows to a table
    public static function insert ($values) {
        foreach ($values as $key => $value) $keys[] = '`' . $key . '`';
        return Database::query('INSERT INTO `' . static::table() . '` (' . implode(', ', $keys) . ') ' .
            'VALUES (' . implode(', ', array_fill(0, count($values), '?')) . ')', ...array_values($values));
    }

    // A query function that updates rows in a table
    public static function update ($where, $values) {
        if (!is_array($where)) $where = [ static::$primaryKey => $where ];
        foreach ($values as $key => $value) $sets[] = '`' . $key . '` = ?';
        foreach ($where as $key => $value) $wheres[] = '`' . $key . '` = ?';
        return Database::query('UPDATE `' . static::table() . '` SET ' . implode(', ', $sets) . ' ' .
            'WHERE ' . implode(' AND ', $wheres), ...array_merge(array_values($values), array_values($where)));
    }

    // A query function that deletes rows in a table
    public static function delete ($where) {
        if (!is_array($where)) $where = [ static::$primaryKey => $where ];
        foreach ($where as $key => $value) $wheres[] = '`' . $key . '` = ?';
        return Database::query('DELETE FROM `' . static::table() . '` WHERE ' . implode(' AND ', $wheres), ...array_values($where));
    }

    // A query function that counts rows in a table
    public static function count ($where = null) {
        if (is_null($where)) {
            return Database::query('SELECT COUNT(`' . static::$primaryKey . '`) as count FROM `' . static::table() . '`')->fetch()->count;
        } else {
            if (!is_array($where)) $where = [ static::$primaryKey => $where ];
            foreach ($where as $key => $value) $wheres[] = '`' . $key . '` = ?';
            return Database::query('SELECT COUNT(`' . static::$primaryKey . '`) as count FROM `' . static::table() . '` WHERE ' . implode(' AND ', $wheres), ...array_values($where))->fetch()->count;
        }
    }
}
