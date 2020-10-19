<?php namespace System;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | Crud Settings
  |--------------------------------------------------------------------------
  |
 */
class Crud extends \System\Database
{
    // merupakan fungsi untuk melihat tabel dari database ( select * from )
    function get($tabel, $order = null)
    {
        return $this->db->query("SELECT * FROM $tabel $order");
    }

    // merupakan fungsi untuk melihat data table dari database berdasarkan id
    function get_where($tabel,$where)
    {
        $key = array_keys($where);
        $val = array_values($where);

        if(count($key) > 0)
        {
            $where = implode('=? AND ', $key).'=?';
        }else{
            $where = implode('=? ', $key);
        }

        $row = $this->db->prepare("SELECT * FROM $tabel WHERE $where");
        $row->execute($val);
        return $row;
    }

    // merupakan fungsi untuk melihat data table dari database berdasarkan id
    function get_where_or($tabel,$where)
    {
        $key = array_keys($where);
        $val = array_values($where);

        if(count($key) > 0)
        {
            $where = implode('=? OR ', $key).'=?';
        }else{
            $where = implode('=? ', $key);
        }

        $row = $this->db->prepare("SELECT * FROM $tabel WHERE $where");
        $row->execute($val);
        return $row;
    }

    // merupakan fungsi untuk tambah data
    function insert($tabel,$paramsArr)
    {
        $key = array_keys($paramsArr);
        $val = array_values($paramsArr);

        //sanitation needed!
        $query = "INSERT INTO $tabel (" . implode(', ', $key) . ") "
             . "VALUES ('" . implode("', '", $val) . "')";

        $row = $this->db->prepare($query);
        return $row ->execute();
    }

    // merupakan fungsi edit data
    function update($tabel,$data,$where,$id)
    {
        $setPart = array();
        foreach ($data as $key => $value)
        {
            $setPart[] = $key."=:".$key;
        }
        $sql = "UPDATE $tabel SET ".implode(', ', $setPart)." WHERE $where = :id";
        $row = $this->db->prepare($sql);
        //Bind our values.
        $row ->bindValue(':id',$id); // where
        foreach($data as $param => $val)
        {
    
            $row ->bindValue($param, $val);
        }
        return $row ->execute();
    }
    
    // merupakan fungsi untuk hapus data
    function delete($tabel,$where,$id)
    {
        $sql = "DELETE FROM $tabel WHERE $where = ?";
        $row = $this->db->prepare($sql);
        return $row ->execute(array($id));
    }

}