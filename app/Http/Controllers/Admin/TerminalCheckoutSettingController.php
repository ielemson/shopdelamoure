<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
class TerminalCheckoutSettingController extends Controller
{
    // Retire the duplicate settings workflow; existing menu/routes open the single live setup.
    public function edit() { return redirect()->route('admin.delivery.settings'); }
    public function update() { return redirect()->route('admin.delivery.settings')->with('success', 'Use the unified Delivery Settings page to save your live configuration.'); }
    public function preview() { return redirect()->route('admin.delivery.settings'); }
    public function stop() { return redirect()->route('admin.delivery.settings'); }
}
