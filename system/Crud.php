<?php
defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');
/*
  |--------------------------------------------------------------------------
  | Crud Settings
  |--------------------------------------------------------------------------
  |
 */
class Crud {

    protected $db;
    function __construct($db){
        $this->db = $db;
    }

    // merupakan fungsi untuk melihat tabel dari database ( select * from )
    function get($tabel)
    {
        return $this->db->query("SELECT * FROM $tabel");
    }

    // merupakan fungsi untuk melihat data table dari database berdasarkan id
    function get_where($tabel,$where,$id)
    {
        $row = $this->db->prepare("SELECT * FROM $tabel WHERE $where = ?");
        $row->execute(array($id));
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