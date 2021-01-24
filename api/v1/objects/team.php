<?php
class team
{

    // database connection and table name
    private $conn;
    private $table_name = "teams";

    // object properties
    public $id;
    public $name;
    public $image;
    public $region;
    public $description;
    public $created;
    public $createdby;
    public $modified;
    public $modifiedby;
    public $active;
    public $flag;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getTeams()
    {

        // select all query
        $query = "SELECT * from teams where active=1 order by name";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        $teams = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // extract row
            // this will make $row['name'] to
            // just $name only

            $team_item = array(
                "id" => $row['id'],
                "name" => $row['name'],
                "image" => $row['image'],
                "region" => $row['region'],
                "description" => $row['description'],
                "created" => $row['created'],
                "createdby" => $row['createdby'],
                "modified" => $row['modified'],
                "modifiedby" => $row['modifiedby'],
                "active" => $row['active'],
                "flag" => $row['flag']
            );

            array_push($teams, $team_item);
        }

        return json_encode($teams);
    }

    public function getTeamById($id)
    {

        // select all query
        $query = "SELECT
        t.*, u.usertag AS cutag
    FROM
        teams t
    LEFT JOIN users u ON u.user_id = t.createdby
    WHERE
        id = $id";
        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        $team = $stmt->fetch(PDO::FETCH_ASSOC);

        return json_encode($team);
    }

    public function deleteTeam($id)
    {

        //  query to inactivate
        $query = "update teams set active=0 where 1 and id = ?";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind id of record to delete
        $stmt->bindParam(1, $id, PDO::PARAM_INT);

        return json_encode(["success" => $stmt->execute()]);
    }

    public function createTeam($data)
    {
        try {

            // query to insert record
            $query = "INSERT INTO " . $this->table_name .
                " SET id=0,
                  name=:name,
                  image=:image,
                  region=:region,
                  description=:description,
                  createdby=:createdby,
                  modifiedby=:modifiedby,
                  active=1,
                  flag=0";

            // prepare query
            $stmt = $this->conn->prepare($query);

            // sanitize
            $this->name = htmlspecialchars(strip_tags($data['name']));
            $this->image = htmlspecialchars(strip_tags($data['image']));
            $this->region = htmlspecialchars(strip_tags($data['region']));
            $this->description = htmlspecialchars(strip_tags($data['description']));
            $this->createdby = htmlspecialchars(strip_tags($data['createdby']));
            $this->modifiedby = htmlspecialchars(strip_tags($data['modifiedby']));

            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":image", $this->image);
            $stmt->bindParam(":region", $this->region);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":createdby", $this->createdby);
            $stmt->bindParam(":modifiedby", $this->modifiedby);

            // // execute query
            // return json_encode(["success" => $stmt->execute()]);
            if($stmt->execute()){
                $team_name = $this->name;
                $player_id = $this->createdby;
                
                // echo $team_name;
                       // select all query
        $getidquery = "SELECT * from teams where 1 and name='$team_name' ";

        // echo $query;
        // prepare query statement
        $stmt = $this->conn->prepare($getidquery);

        // execute query
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $team_id = $row['id'];

        // echo $team_id;

        $removed = 0;

                echo $this->insertPlayer($team_id,$player_id,$removed);
            }else{
                http_response_code(400);
                echo json_encode(array("message" => "False"));
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }
    public function insertPlayer($team_id,$player_id,$removed)
    {
        try {
            //code...

            // query to insert record
            $query = "INSERT INTO team_player SET
          team_id=:team_id,
          player_id=:player_id,
          removed =:removed
          ";

            // prepare query statement
            $stmt = $this->conn->prepare($query);

            // bind parameters
            $stmt->bindParam(":team_id", $team_id);
            $stmt->bindParam(":player_id", $player_id);
            $stmt->bindParam(":removed", $removed);


            // execute query
            if($stmt->execute()){
            
            $updatequery = "UPDATE users SET role = '3' where user_id='$player_id'";

            // prepare query statement
            $uptstmt = $this->conn->prepare($updatequery);
        
            echo json_encode(
                array(
                    "Player Insertion" => true,
                    "team" => $team_id,
                    "Role Update" =>  $uptstmt->execute()
                )
            );
        }
            // return $team_id;
        } catch (PDOException $e) {
            throw $e;
        }
    }


    public function updateTeam($id, $data)
    {
        try {

            // query to insert record
            $query =  "UPDATE  $this->table_name " . "
                  SET
                  name=:name,
                  image=:image,
                  region=:region,
                  description=:description,
                  created=:created,
                  createdby=:createdby,
                  modified=:modified,
                  modifiedby=:modifiedby,
                  active=:active,
                  flag=:flag
                  WHERE id = :id";

            // prepare query
            $stmt = $this->conn->prepare($query);

            // sanitize
            $this->name = htmlspecialchars(strip_tags($data['name']));
            $this->image = htmlspecialchars(strip_tags($data['image']));
            $this->region = htmlspecialchars(strip_tags($data['region']));
            $this->description = htmlspecialchars(strip_tags($data['description']));
            $this->created = htmlspecialchars(strip_tags($data['created']));
            $this->createdby = htmlspecialchars(strip_tags($data['createdby']));
            $this->modified = htmlspecialchars(strip_tags($data['modified']));
            $this->modifiedby = htmlspecialchars(strip_tags($data['modifiedby']));
            $this->active = htmlspecialchars(strip_tags($data['active']));
            $this->flag = htmlspecialchars(strip_tags($data['flag']));


            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":image", $this->image);
            $stmt->bindParam(":region", $this->region);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":created", $this->created);
            $stmt->bindParam(":createdby", $this->createdby);
            $stmt->bindParam(":modified", $this->modified);
            $stmt->bindParam(":modifiedby", $this->modifiedby);
            $stmt->bindParam(":active", $this->active);
            $stmt->bindParam(":flag", $this->flag);
            $stmt->bindParam(":id", $id);

            // execute query
            return json_encode(["success" => $stmt->execute()]);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
