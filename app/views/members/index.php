<h2>Members</h2>
<p><a href="/members/register">Register new member.</a></p>
<?php if(count($items) > 0):?>
    <table class="alternating-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item):?>
                <tr>
                    <td><?php echo h($item['username']);?></td>
                    <td><a href="/members/view/<?php echo $item['id'];?>" style="font-size:0.8em">More Info</a></td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
<?php else:?>
    <p>There are no members registered.</p>
<?php endif;?>
