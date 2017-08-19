<?php

// Every controller must extend "Controller".
// And the class name is always the same as the filename.

class PostsController extends Controller
{
    public function index()
    {
        // Users can get to this function. By going to http://example.com/posts or http://example.com/posts/index
        
        // The Post class extends "Model".
        $m = new Post();
        
        // To get every row from the database, call the findAll function.
        $items = $m->findAll();
        
        // As you may the guess, "app\views\posts\index" would be the view file.
        // To give variables to the views, call the set function.
        $this->set('items', $items);
    }

    public function add()
    {
    
        // This locks the page, if the user logged in doesn't have the permission.
        $this->_lockWithPermission('posts.edit');
        
        // Checks if the current request is a POST request.
        
        if ($this->is('post')) {
            
            // Note: A POST request is not the same as this Post model.
            $m = new Post();
            
            // Attempts to insert a row by using an array of keys and values.
            // Here, $_POST['Post'] is assumed to be an array of keys and values (A.K.A. an associative array.)
            $m->create($_POST['Post']);
            
            // The static function, "Session::setFlash" sets a flash message.
            // A flash message is a message that is displayed on the next request.
            Session::setFlash('Added this post successfully!', 'success');
            
            // This redirects to the url specified.
            // The function, "lastInsertId" of a model, returns the ID of the last row created.
            return $this->redirect('/posts/view/' . $m->lastInsertId());
        }
    }

    public function view($id)
    {
        $this->_lockWithPermission('posts.view');

        $m = new Post();
        
        // findById tries to find one post that has an "id" of $id
        // You can also use "findByUsername" to find one post with a specific "username",
        //         "findBySkillPoints" to find one post with a specific "skill_points",
        //         "findAllBySpellAndType to find all posts with a specific "spell" AND "type".
        $item = $m->findById($id);
        if ($item === null) {
            Session::setFlash('Unable to find post!', 'error');
            return $this->redirect('/posts');
        }

        $this->set('item', $item);

    }

    public function edit($id)
    {
        $this->_lockWithPermission('posts.edit');
        $m = new Post();
        if ($this->is('post')) {
            if (isset($_POST['Post'])) {
			
                // array_filter is a PHP function. It filters each item in an array.
                // See the PHP.net documentation.
                $data = array_filter($_POST['Post'], 'strlen');
				
                if (count($data) > 0) {
                    
                    // updateBy<stuff> works the same way as the findBy<stuff> functions.
                    // You can updateById, updateAllByType, updateByTypeAndPhone, etc.
                    // The last parameter is the data which is going to be updated. It must be an associative array.
                    $m->updateById($id, $data);
                    
                    Session::setFlash('Updated post successfully!', 'success');
                    
                } else {
                    Session::setFlash('Nothing was updated.', 'info');
                }
				
            }

            return $this->redirect('/posts/view/' . $id);
        }
        $item = $m->findById($id);
        if ($item === null) {
            Session::setFlash('Unable to find posts!', 'error');
            $this->redirect('/posts');
			return;
        }
        $this->set('item', $item);
    }

    public function delete($id)
    {
        $this->_lockWithPermission('posts.edit');
        if ($this->is('post')) {
            $m = new Post();
            
            // Same as updateBy<stuff>, and findBy<stuff>.
            $m->deleteById($id);
            Session::setFlash('Deleted post successfully!', 'success');
        }
        $this->redirect('/posts');
    }

}
