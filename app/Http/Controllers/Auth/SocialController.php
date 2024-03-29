<?php
namespace App\Http\Controllers\Auth;
use App\User;
use Socialite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
class SocialController extends Controller
{
    /**
     * Redirects to appropriate providers based on
     * $provider
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function redirectToProvider() : RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }
    /**
     * Undocumented function
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function handleProviderCallback() : RedirectResponse
    {
        try {
            $data = Socialite::driver('facebook')->user();
            return $this->handleUser($data, $provider);
        } catch (\Exception $e) {

            return redirect(route('login'))->with('status', 'Facebook Login failed');
        }
    }
    /**
     * Handles the user's information and creates/updates
     * the record accordingly.
     *
     * @param object $data
     * @param string $provider
     * @return RedirectResponse
     */
    public function handleUser(object $data) : RedirectResponse
    {
        $user = User::where([
            'social->'.$provider.'->id' => $data->id,
        ])->first();

        if (!$user) {
            

            $user = User::where([
                'email' => $data->email,
            ])->first();
        }

        if (!$user) {

            return $this->createUser($data, $provider);
        }

        $user->social->facebook->token = $data->token;
        $user->save();
        
        return $this->login($user);
    }
    /**
     * Undocumented function
     *
     * @param object $data
     * @param string $provider
     * @return RedirectResponse
     */
    public function createUser(object $data) : RedirectResponse
    {
        try {

            $user = new User;
            $user->name   = $data->name;
            $user->email  = $data->email;
            $user->social = json_encode([
                $provider => [
                    'id'    => $data->id,
                    'token' => $data->token
                ]
            ]);

            $user->save();
            return $this->login($user);
        }

         catch (Exception $e) {
            return redirect(route('login'))->with(['status' => 'Login failed. Please try again']);
        }
    }
    /**
     * Logins the given user and redirects to home
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function login(User $user) : RedirectResponse
    {
        auth()->loginUsingId($user->id);
        return redirect(route('home'));
    }
}