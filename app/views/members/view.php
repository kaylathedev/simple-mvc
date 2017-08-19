<a href="/members">Go Back</a>
<h2>Member Info</h2>
<div style="float:right">
<form action="/members/delete/<?php echo $item['id'];?>" method="POST">
<input type="submit" value="Delete">
</form>
</div>
<table>
    <tr>
        <td>Username</td>
        <td><?php echo h($item['username']);?></td>
    </tr>
    <tr>
        <td>Password</td>
        <td>*********</td>
    </tr>
</table>
<form method="POST" action="/members/change/<?php echo $item['id']?>/password">
    <label>Change Password: <input type="password" name="Account[password]" placeholder="New password"></label>
    <input type="submit" value="Change">
</form>
<h2>Roles</h2>
<div class="flow-fake-table">
<?php foreach($roles as $role):?>
    <div style="float:left;margin:0 5px;text-align:center">
        <div style="background:#fff;border:2px solid #000;line-height:2em;margin:0 0 4px 0;min-width:80px">
            <?php echo $role;?>
        </div>
        <form method="POST" action="/members/change/<?php echo $item['id']?>/removerole">
            <input type="hidden" name="Role[value]" value="<?php echo $role;?>">
            <input type="submit" value="Remove">
        </form>
    </div>
<?php endforeach;?>
</div>
<div style="clear:both"></div>
<br>
<form method="POST" action="/members/change/<?php echo $item['id']?>/role">
    <select name="Role[value]">
        <?php foreach($allRoles as $role):?>
            <option value="<?php echo $role;?>"><?php echo Language::titleize($role);?></option>
        <?php endforeach;?>
    </select>
    <input type="submit" value="Add Role">
</form>
