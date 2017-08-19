<h2>Posts</h2>
<p><a href="/posts/add">Add new posts.</a></p>
<?php if(count($items) > 0):?>
<table class="alternating-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $item):?>
            <tr>
                <td><?php echo h($item['title']);?></td>
                <td><a href="/posts/view/<?php echo $item['id'];?>">More Info</a></td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>
<?php else:?>
    <p>There are no posts.</p>
<?php endif;?>
