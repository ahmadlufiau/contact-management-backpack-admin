<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ContactRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ContactCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ContactCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Contact::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/contact');
        CRUD::setEntityNameStrings('contact', 'contacts');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id')->label('ID');
        CRUD::column('first_name')->label('First Name');
        CRUD::column('last_name')->label('Last Name');
        CRUD::column('email')->label('Email');
        CRUD::column('phone')->label('Phone');
        CRUD::column('company')->label('Company');
        CRUD::column('created_at')->label('Created')->type('datetime');

        // Add search functionality
        CRUD::addFilter([
            'type' => 'text',
            'name' => 'search',
            'label' => 'Search'
        ], false, function ($value) {
            CRUD::addClause('where', function ($query) use ($value) {
                $query->where('first_name', 'like', '%' . $value . '%')
                      ->orWhere('last_name', 'like', '%' . $value . '%')
                      ->orWhere('email', 'like', '%' . $value . '%')
                      ->orWhere('company', 'like', '%' . $value . '%');
            });
        });
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ContactRequest::class);

        CRUD::field('first_name')->label('First Name')->type('text');
        CRUD::field('last_name')->label('Last Name')->type('text');
        CRUD::field('email')->label('Email')->type('email');
        CRUD::field('phone')->label('Phone')->type('text');
        CRUD::field('company')->label('Company')->type('text');
        CRUD::field('address')->label('Address')->type('textarea');
        CRUD::field('birth_date')->label('Birth Date')->type('date');
        CRUD::field('notes')->label('Notes')->type('textarea');
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     * Define what happens when the Show operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-show
     * @return void
     */
    protected function setupShowOperation()
    {
        CRUD::column('id')->label('ID');
        CRUD::column('first_name')->label('First Name');
        CRUD::column('last_name')->label('Last Name');
        CRUD::column('email')->label('Email');
        CRUD::column('phone')->label('Phone');
        CRUD::column('company')->label('Company');
        CRUD::column('address')->label('Address');
        CRUD::column('birth_date')->label('Birth Date')->type('date');
        CRUD::column('notes')->label('Notes');
        CRUD::column('created_at')->label('Created')->type('datetime');
        CRUD::column('updated_at')->label('Updated')->type('datetime');
    }
}
