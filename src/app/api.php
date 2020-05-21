<?php
namespace app;

class Api {

    // Json decode 
    protected static function getJson( $content ){
        return json_encode( $content );
    }

    // Update number increment
    protected static function updateIncrement($data){
        $inc = $data->increment;    // Current increment
        $data->increment = $inc+1;  // Making one point more

        return $data;
    }

    // Create result array
    protected static function result($type, $mess = null){
        $res = array(
            "result" => $type,  // Response type
            "message" => $mess  // Response body 
        );
        
        // Translate in json
        return self::getJson($res);
    }

    // Add user function
    public static function addUser( $add ){

        // Testing the string for void
        if ( !$add ) 
            return self::result("ok", 'Параметр "имя" пустой!');

        $data = JsonDB::insert(["name" => $add]); // Insert new user

        // We return the result of the request
        if ( $data ) {
            $res = self::result("ok", "Пользователь успешно создан!");
        } else {
            $res = self::result("error", "Ошибка создания пользователя!");
        }

        return $res;
    }

    // Get all users
    public static function getUsers() {
       $data = JsonDB::selectAll();  // Get all users
    
       // We return the result of the request
       if ( count($data) ) {
            $res = array();

            // Format the response to a suitable return
            foreach ($data as $key => $value) {
                $res[] = [
                    "id"    => $value["id"],
                    "name"  => $value["name"]
                ];
            }

            return self::result("ok", $res);
       } else {
            return self::result("error", "Нет пользователей!");
       }
    }

    // Display user parameters
    public static function getUser($id) {

        // Testing the string for validity
        if ( !is_numeric($id) )
            return self::result("error", "Не верный формат запроса!");

        $data = JsonDB::select($id);    // Get user by id

        //  Check if the user is found
        if ( $data ) {
            return self::result("ok", [
                'id'    => $data['data']['id'],
                'name'  => $data['data']['name']
           ]);
        } else {
            return self::result("error", "Пользователь не найден!");
        }
    }

    // Update user name
    public static function UpdateUser( $id, $name ) { 

        // Testing the string for validity
        if ( !is_numeric($id) or !$name ) 
            return self::result("ok", "Не верный формат запроса!");

        $data = JsonDB::update($id, $name);
        
        // Check if user data is updated
        if ( $data ) {
            return self::result("ok", "Имя успешно обновлено!");
        } else {
            return self::result("error", "Не удалось изменить имя!");
        }
    }

    // Function delete user at id
    public static function deleteUser( $id ) { 
        
        // Testing the string for validity
        if ( !is_numeric($id) ) 
            return self::result("error", "Не верный формат запроса!");

        // Check if user is deleted
        if ( JsonDB::delete($id) ) {
            return self::result("ok", "Пользователь удален!");
        } else {
            return self::result("ok", "Пользователь не найден!");
        }
    }
}

?>