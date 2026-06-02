<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('login_code')->nullable()->unique()->after('email');
        });

        // Generate codes for existing users
        $users = User::all();
        $adminCount = 0;
        $employeeCount = 0;

        foreach ($users as $user) {
            $prefix = $user->role === 'admin' ? 'ADM' : 'EMP';
            
            if ($user->role === 'admin') {
                $adminCount++;
                $num = $adminCount;
            } else {
                $employeeCount++;
                $num = $employeeCount;
            }
            
            $user->login_code = $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('login_code');
        });
    }
};
