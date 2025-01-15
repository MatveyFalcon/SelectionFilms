<?php
function getAddedFilms($mysql, $userId) {
    $addedFilms = [];
    if ($userId) {
        $query = $mysql->prepare("
            SELECT DISTINCT film_id 
            FROM collection_films 
            WHERE collection_id IN (
                SELECT id FROM collections WHERE user_id = ?
            )
        ");
        $query->bind_param('i', $userId);
        $query->execute();
        $result = $query->get_result();
        while ($row = $result->fetch_assoc()) {
            $addedFilms[] = $row['film_id'];
        }
    }
    return $addedFilms;
}
?>

