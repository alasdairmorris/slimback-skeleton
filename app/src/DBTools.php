<?php

namespace App;

/**
 * Wrappers around some DB function.
 * Most of them simply add logging.
 */
class DBTools {

    public static function insert($app, $tableName, $data)
    {
        $rec = $app->db->$tableName()->insert($data);
        $recArray = iterator_to_array($rec);
        $app->log->addInfo(
            sprintf("Created $tableName(%d)", $recArray['id']),
            array(
                "new_record" => $recArray
            )
        );

        return $rec;
    }

    public static function update($app, $tableName, $id, $data)
    {
        $rec = $app->db->$tableName("id", $id)->fetch();
        if ($rec) {
            $rec->update($data);
            $app->log->addInfo(
                "Updated $tableName($id)",
                array(
                    "old_record" => iterator_to_array($rec),
                    "new_details" => $data,
                    "diff" => array_diff_assoc($data, iterator_to_array($rec))
                )
            );
        }
        else{
            $msg = "Unable to find $tableName with id " . $id;
            $app->log->addWarning($msg);
            throw new \Exception($msg);
        }

        return $app->db->{$tableName}[$id]; // return the updated record
    }

    public static function delete($app, $tableName, $id)
    {
        $rec = $app->db->$tableName("id", $id)->fetch();
        if ($rec) {
            $oldrec = iterator_to_array($rec);
            $rec->delete();
            $app->log->addInfo("Deleted $tableName($id)", array("old_record" => $oldrec));
        }
        else{
            $msg = "Unable to find $tableName with id " . $id;
            $app->log->addWarning($msg);
            throw new \Exception($msg);
        }
    }

    public static function asJSON($result)
    {
        return json_encode(array_values(iterator_to_array($result)));
    }

}
