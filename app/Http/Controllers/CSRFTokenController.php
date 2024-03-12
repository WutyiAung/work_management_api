<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CSRFTokenController extends Controller
{
    public function clearCSRFToken(Request $request)
    {
        try {
            // Clear CSRF token
            $request->session()->regenerateToken();

            // Return success response
            return response()->json(['message' => 'CSRF token cleared successfully'], 200);
        } catch (\Exception $e) {
            // Return error response
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
