<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ProfileTest extends TestCase {
    use RefreshDatabase;
    public function test_account_settings_require_login(): void { $this->get('/account')->assertRedirect('/login'); }
    public function test_account_settings_are_accessible(): void { $this->actingAs(User::factory()->create())->get('/account')->assertOk(); }
    public function test_account_can_be_updated(): void {
        $user=User::factory()->create();
        $this->actingAs($user)->patch('/account',['name'=>'Sean James','email'=>'sean@example.test'])->assertSessionHasNoErrors()->assertRedirect('/account');
        $this->assertSame('Sean James',$user->fresh()->name);
    }
    public function test_account_deletion_checks_password(): void {
        $user=User::factory()->create();
        $this->actingAs($user)->delete('/account',['password'=>'incorrect'])->assertSessionHasErrorsIn('userDeletion','password');
        $this->assertNotNull($user->fresh());
    }
    public function test_account_can_be_deleted(): void {
        $user=User::factory()->create();
        $this->actingAs($user)->delete('/account',['password'=>'password'])->assertSessionHasNoErrors()->assertRedirect('/');
        $this->assertGuest(); $this->assertNull($user->fresh());
    }
}
