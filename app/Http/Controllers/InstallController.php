<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class InstallController extends Controller
{
    public function index()
    {
        return view('install.index', [
            'installed' => $this->isInstalled(),
        ]);
    }

    public function store(Request $request)
    {
        if ($this->isInstalled()) {
            return redirect()->route('install.index')->withErrors([
                'installer' => 'The application is already installed.',
            ]);
        }

        $data = $request->validate([
            'installer_key' => ['required', 'string'],
            'app_name' => ['required', 'string', 'max:255'],
            'app_url' => ['required', 'url'],
            'db_host' => ['required', 'string'],
            'db_port' => ['required', 'numeric'],
            'db_database' => ['required', 'string'],
            'db_username' => ['required', 'string'],
            'db_password' => ['nullable', 'string'],
        ]);

        if (!$this->isInstallerKeyValid($data['installer_key'])) {
            return back()->withErrors([
                'installer_key' => 'Installer key is invalid.',
            ]);
        }

        $this->writeEnv($data);

        Artisan::call('key:generate', ['--force' => true]);
        Artisan::call('migrate', ['--seed' => true, '--force' => true]);
        Artisan::call('storage:link');

        $this->markInstalled();

        return redirect()->route('login')->with('status', 'Installation complete. You can now log in.');
    }

    private function isInstalled(): bool
    {
        return is_file(storage_path('app/installed.lock'));
    }

    private function markInstalled(): void
    {
        $path = storage_path('app/installed.lock');
        file_put_contents($path, 'installed at '.now());
    }

    private function isInstallerKeyValid(string $key): bool
    {
        $expected = env('INSTALLER_KEY');

        return !empty($expected) && hash_equals($expected, $key);
    }

    private function writeEnv(array $data): void
    {
        $env = <<<ENV
APP_NAME="{$data['app_name']}"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL={$data['app_url']}

DB_CONNECTION=mysql
DB_HOST={$data['db_host']}
DB_PORT={$data['db_port']}
DB_DATABASE={$data['db_database']}
DB_USERNAME={$data['db_username']}
DB_PASSWORD={$data['db_password']}

SESSION_DRIVER=file
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
INSTALLER_KEY={$data['installer_key']}
ENV;

        file_put_contents(base_path('.env'), $env);
    }
}
