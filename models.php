<?php
 class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "contact_app";
    public $conn;

    public function getConnection(){
        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Koneksi database gagal: " . $exception->getMessage();
        }

        return $this->conn;
    }
}   
?>

<?php
class Contact {
    private $conn;
    private $table_name = "contacts";

    public $id;
    public $nomor;
    public $owner;

    public function __construct($db){
        $this->conn = $db;
    }

    function create(){
        $query = "INSERT INTO " . $this->table_name . " SET nomor=:nomor, owner=:owner";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nomor", $this->nomor);
        $stmt->bindParam(":owner", $this->owner);

        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function read(){
        $query = "SELECT id, nomor, owner FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function update(){
        $query = "UPDATE " . $this->table_name . " SET nomor=:nomor, owner=:owner WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nomor", $this->nomor);
        $stmt->bindParam(":owner", $this->owner);

        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id);

        if($stmt->execute()){
            return true;
        }

        return false;
    }
}
?>
<?php
include_once 'models.php';

$database = new Database();
$db = $database->getConnection();

$contact = new Contact($db);

$stmt = $contact->read();
$num = $stmt->rowCount();

if($num>0){
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        echo "ID: {$id}, Nomor: {$nomor}, Owner: {$owner}<br>";
    }
} else {
    echo "Tidak ada kontak yang ditemukan.";
}

$contact->id = 1;
$contact->nomor = "082272039299";
$contact->owner = "Arrel Kurniawan";
if($contact->update()){
    echo "Kontak berhasil diperbarui.";
} else{
    echo "Gagal memperbarui kontak.";
}

?>