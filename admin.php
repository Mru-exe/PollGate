<?php
define('PAGE_TITLE', 'PollGate | Admin Panel');
require_once "src/common.php";
continueSession(true, "", true);
require_once "src/dbcontext.php";

$users = $db->getUsers(20, 0)
//TODO: PASSWORD RESET

?>

<!--HTML STARTS HERE-->
<?php require_once "public/partials/header.php"; ?>
<script src="public/assets/js/admin.js" defer></script>
<main class="default" id="admin">
    <div class="card container-flex-col">
        <span class="div-title">User management</span>
        <table id="user-table">
        <thead>
            <tr>
            <th>[ID] Username</th>
            <th>Role</th>
            <th>Registered</th>
            <th>Last Modified</th>
            <th>Actions</th>
            <!-- <th>Inspect</th> -->
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($users as $user){
                echo "<tr class='clickable'>";
                echo "<td> [$user->id]  " . htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlspecialchars($user->roleName, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . date_format($user->created, "m/d/Y | H:i:s") . "</td>";
                echo "<td>" . ($user->modified ? date_format($user->modified, "m/d/Y | H:i:s") : " - ") . "</td>";
                if($user->roleId > 2){
                    echo '<td><div class="user-actions"><a href="api/adminActions.php?action=promote&user-id='.$user->id.'" class="btn bg-blue">Promote</a><a href="reset-password.php?&user-id='.$user->id.'" class="btn bg-red">Reset Password</a></div></td>';
                } else {
                    echo '<td></td>';
                }
                echo "</tr>";
            }
            ?>
        </tbody>
        </table>
    </div>
</main>
<?php require_once "public/partials/footer.php"; ?>