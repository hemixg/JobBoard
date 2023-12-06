<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
session_start();


require_once "database.php";

$response = array();

if (isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT 
            IF(p.role = 'user' OR p.role = 'admin', CONCAT(p.first_name, ' ', p.last_name), c.name) AS user_name
        FROM people p
        RIGHT JOIN companies c ON p.id = c.id
        WHERE p.id = ? OR c.id = ?";
        
    $stmt = mysqli_stmt_init($conn);

    if ($stmt){
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            $response['user_profile'] = $user;
        } else {
            $response['error'] = "Profil non trouvé";
        }
    } else  {
        $response['error'] = 'Echec de la requete SQL: ' . mysqli_error($conn);
    }
} else {
    $response['error'] = "L'utilisateur n'est pas connecté";
}

echo json_encode($response);
?>
