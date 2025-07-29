<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ContactApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->token = $response->json('data.token');
    }

    public function test_user_can_login_and_get_token()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Login successful'
            ]);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
    }

    public function test_login_validation_errors()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'email',
                    'password'
                ]
            ]);
    }

    public function test_authenticated_user_can_get_contacts()
    {
        Contact::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'phone',
                        'company',
                        'address',
                        'birth_date',
                        'notes',
                        'full_name',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Contacts retrieved successfully'
            ]);

        $this->assertCount(5, $response->json('data'));
    }

    public function test_user_can_create_contact()
    {
        $contactData = [
            'first_name' => 'Ahmad',
            'last_name' => 'Lufi',
            'email' => 'ahmad.lufi@example.com',
            'phone' => '+1234567890',
            'company' => 'Test Company',
            'address' => '123 Test Street',
            'birth_date' => '1990-01-01',
            'notes' => 'Test notes'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contacts', $contactData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'company',
                    'address',
                    'birth_date',
                    'notes',
                    'full_name',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Contact created successfully',
                'data' => [
                    'first_name' => 'Ahmad',
                    'last_name' => 'Lufi',
                    'email' => 'ahmad.lufi@example.com',
                    'full_name' => 'Ahmad Lufi'
                ]
            ]);

        $this->assertDatabaseHas('contacts', [
            'email' => 'ahmad.lufi@example.com',
            'first_name' => 'Ahmad',
            'last_name' => 'Lufi',
        ]);
    }

    public function test_contact_creation_validation_errors()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/contacts', [
            'first_name' => '',
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'first_name',
                    'last_name',
                    'email'
                ]
            ]);
    }

    public function test_user_can_get_single_contact()
    {
        $contact = Contact::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'company',
                    'address',
                    'birth_date',
                    'notes',
                    'full_name',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Contact retrieved successfully',
                'data' => [
                    'id' => $contact->id,
                    'first_name' => $contact->first_name,
                    'last_name' => $contact->last_name,
                    'email' => $contact->email
                ]
            ]);
    }

    public function test_user_can_update_contact()
    {
        $contact = Contact::factory()->create();

        $updateData = [
            'first_name' => 'Ahmad',
            'last_name' => 'Lufi',
            'email' => 'ahmad.lufi@example.com',
            'phone' => '+0987654321',
            'company' => 'Updated Company'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/contacts/{$contact->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'company',
                    'full_name',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Contact updated successfully',
                'data' => [
                    'id' => $contact->id,
                    'first_name' => 'Ahmad',
                    'last_name' => 'Lufi',
                    'email' => 'ahmad.lufi@example.com',
                    'full_name' => 'Ahmad Lufi'
                ]
            ]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => 'Ahmad',
            'last_name' => 'Lufi',
            'email' => 'ahmad.lufi@example.com',
        ]);
    }

    public function test_user_can_delete_contact()
    {
        $contact = Contact::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Contact deleted successfully'
            ]);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id
        ]);
    }

    public function test_contacts_can_be_searched()
    {
        Contact::factory()->create(['first_name' => 'Joko', 'last_name' => 'Silu']);
        Contact::factory()->create(['first_name' => 'Ula', 'last_name' => 'Gas']);
        Contact::factory()->create(['first_name' => 'Ahmad', 'last_name' => 'Son']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/contacts?search=Joko');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_contacts_can_be_filtered_by_company()
    {
        Contact::factory()->create(['company' => 'Apple Inc']);
        Contact::factory()->create(['company' => 'Google LLC']);
        Contact::factory()->create(['company' => 'Microsoft Corp']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/contacts?company=Apple');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Apple Inc', $data[0]['company']);
    }

    public function test_contacts_pagination()
    {
        Contact::factory()->count(15)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/contacts?per_page=10');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $data = $response->json('data');
        $meta = $response->json('meta');

        $this->assertCount(10, $data);
        $this->assertEquals(15, $meta['total']);
        $this->assertEquals(2, $meta['last_page']);
        $this->assertEquals(1, $meta['current_page']);
    }

    public function test_user_can_logout()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
    }

    public function test_user_can_get_their_profile()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => [
                    'user' => [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'email' => $this->user->email
                    ]
                ]
            ]);
    }
} 