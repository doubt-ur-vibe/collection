<?php
require_once 'function.php';

/**
 * Takes the user's input and trims it, then removes potentially malicious HTML
 * @param string $userInput
 * @return string
 */
function cleanUserInput(string $userInput): string {
    $trimmedUserInput = trim($userInput);
    $cleanedUserInput = htmlspecialchars($trimmedUserInput);
    return $cleanedUserInput;
}



/**
 * Checks that all form inputs are set, then add's form data to mySQL database
 * @return NULL
 */
function validateFormData() {
    if(isset($_POST['name']) && isset($_POST['img']) && isset($_POST['brand']) && isset($_POST['price'])) {

        $queryString = "&action={$_POST['action']}";

        if ($_POST['action'] == 'edit') {
            $queryString .= "&id={$_POST['id']}";
        }



        if(isset($_POST['id'])) {
            $idInput = cleanUserInput($_POST['id']);
            if(!is_numeric($idInput)) {
                header('Location: index.php?error=id must be a number!?');
                exit();
            }
        }

        $nameInput = cleanUserInput($_POST['name']);
        if (strlen($nameInput) == 0) {
            header("Location: index.php?error=name cannot be empty!$queryString");
            exit();
        }

        $imgInput = cleanUserInput($_POST['img']);
        $brandInput = cleanUserInput($_POST['brand']);
        if (strlen($brandInput) == 0) {
            header('Location: index.php?error=brand cannot be empty!');
            exit();
        }
        $limitedInput = isset($_POST['limited']) ? 1 : 0;
        $priceInput = $_POST['price'];
        if (!is_numeric($priceInput) || $priceInput <= 0) {
            header('Location: index.php?error=price must be a number!');
            exit();
        }

        $validData = [
            'name' => $nameInput,
            'img' => $imgInput,
            'brand' => $brandInput,
            'limited' => $limitedInput,
            'price' => $priceInput
        ];

        if (isset($idInput)) {
            $validData['id'] = $idInput;
        }
        return $validData;
    }
}

/**
 * Takes validated form data and uses it to add a deck of cards to the database
 * @param $validData
 * @return void
 */
function addToDatabase($validData)
{
    $db = getDBConnection();
    $query = $db->prepare("INSERT INTO `playing_cards` (`name`, `brand`, `price`, `img`, `limited`) VALUES (:name, :brand, :price, :img, :limited);");
    $query->execute([
        ':name' => $validData['name'],
        ':img' => $validData['img'],
        ':brand' => $validData['brand'],
        ':limited' => $validData['limited'],
        ':price' => $validData['price'],
    ]);
    header("Location: index.php");
}

/**
 * Takes validated form data and uses it to edit data about deck of cards in the database
 * @param $validData
 * @return void
 */
function editDatabase($validData)
{
    $db = getDBConnection();
    $query = $db->prepare("UPDATE `playing_cards` SET `name` = :name, `brand` = :brand, `price` = :price, `img` = :img, `limited` = :limited WHERE `id` = :id;");
    $query->execute([
        ':id' => $validData['id'],
        ':name' => $validData['name'],
        ':img' => $validData['img'],
        ':brand' => $validData['brand'],
        ':limited' => $validData['limited'],
        ':price' => $validData['price'],
    ]);
    header("Location: index.php");
}

/**
 * Deletes a deck of cards from the database once a user has requested it
 * @param $id
 * @return void
 */
function deleteDatabaseItem($id) {
    $db = getDBConnection();
    $query = $db->prepare("UPDATE `playing_cards` SET `deleted` = 1 WHERE `id` = :id;");
    $query->bindParam(':id', $id);
    $query->execute();
    header("Location: index.php");
}
?>