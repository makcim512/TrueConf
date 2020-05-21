<?
use app\JsonDB;

namespace app;

class JsonDB {
    
    public static $path = __DIR__ . '/../db/database.json';

    // created database file
    protected static function creadFile(){
        $creatFile = fopen(self::$path, "w"); // Создаем сам файл

        // Создаем структуру новой базы
        $newBD = [
            "increment" => 0,   // Инкремент
            "content"   => []   // Контент
        ];

        $newBD = json_encode($newBD);

        fwrite($creatFile, $newBD); // Записываем структуру в файл
        fclose($creatFile);         // Закрываем файл

        return $newBD;
    }

    // Update database file
    protected static function UpdateFile($data){
        
        $data = json_encode($data);

        if ( !file_put_contents(self::$path, $data) ) {
            //Controller::LogError("Error to insert user!");
            return false;
        } else {
            return true;
        }
    }

    // Update number increment
    protected static function updateIncrement($data){
        $inc = $data['increment'];
        $data['increment'] = $inc +1;

        return $data;
    }

    protected static function searchString( $id ) {
        $data   = self::selectAll();
        $res    = false; 

        foreach ($data as $key => $val) {
            if( $val["id"] == $id ) {
                $res = ['index' => $key, 'data' => $val];
                continue;
            }
        }

        return $res;
    }

    // open JSON file
    public static function openFile(){
        // Если файл не существует, то создаем его
        if ( !file_exists( self::$path ) ) {
            $data = self::creadFile();
        } else {
            $data = file_get_contents(self::$path);
        }
        
        return json_decode($data, true);
    }

    // Insert user
    public static function insert( $parametrs ){
        $data = self ::openFile();
        $data = self::updateIncrement($data);

        $data["users"][] = array(
            "id"    => $data['increment'],
            "name"  => $parametrs["name"],
        );
        
        return self::UpdateFile($data);
    }

    // Get all users
    public static function selectAll(){
        $data   = self::openFile();
        return $data['users'];
    }

    public static function select($id){
        return self::searchString($id);
    }

    // Update string
    public static function update($id, $name){
        $db     = self::openFile();
        $index  = self::searchString($id);

        if ( $index ) {
            $db["users"][$index["index"]]['name'] = $name;
            return self::UpdateFile($db);
        } else {
            return false;
        }
    }

    // Delete string
    public static function delete( $id ){
        $db     = self::openFile();
        $index  = self::searchString($id);

        if ( $index ) { 
            unset($db['users'][$index['index']]);
            sort($db['users']);
            return self::UpdateFile($db);
        } else {
            return false;
        }
    }
}

?>