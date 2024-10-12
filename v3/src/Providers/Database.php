<?php

namespace App\Providers;

use PDO;

class Database
{
    protected $pdo;
    protected $table;
    protected $fields = '*';
    protected $wheres = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function select($fields)
    {
        $this->fields = $fields;
    }

    public function where($column, $operator, $value)
    {
        $this->wheres[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];

        return $this;
    }

    public function get()
    {
        $sql = 'SELECT ' . $this->fields
            . ' FROM ' . $this->table;
        
        if(!empty($this->wheres)) {
            $sql .= ' WHERE';
            foreach($this->wheres as $index => $where) {
                if($index > 0) {
                    $sql .= $where['type'] . ' ';
                }
                $sql .= $where['column'] . ' '
                    . $where['operator']
                    . ' ?';
            }
        }

        $stmt = $this->pdo->prepare($sql);
        $bindedValues = array_column($this->wheres, 'value');
        $stmt->execute($bindedValues);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


}