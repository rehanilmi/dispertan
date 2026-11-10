<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('layanans') || ! Schema::hasColumn('layanans', 'icon')) return;
        foreach ([
            'bi-folder2-open' => 'folder-open', 'bi-file-earmark-text' => 'file-text', 'bi-journal-text' => 'notebook-text', 'bi-book' => 'book-open',
            'bi-people-fill' => 'users', 'bi-person-badge' => 'user-round-check', 'bi-geo-alt-fill' => 'map-pin', 'bi-map' => 'map', 'bi-flower1' => 'sprout',
            'bi-flower2' => 'flower-2', 'bi-tree' => 'trees', 'bi-sun' => 'sun', 'bi-cloud-sun' => 'cloud-sun', 'bi-droplet-half' => 'droplets',
            'bi-basket' => 'shopping-basket', 'bi-bag' => 'shopping-bag', 'bi-cart3' => 'shopping-cart', 'bi-tags' => 'tags', 'bi-cash-coin' => 'wallet',
            'bi-graph-up-arrow' => 'trending-up', 'bi-bar-chart-line' => 'activity', 'bi-bug' => 'bug', 'bi-capsule' => 'pill', 'bi-heart-pulse' => 'heart-pulse',
            'bi-clipboard2-pulse' => 'stethoscope', 'bi-ticket-perforated' => 'ticket', 'bi-calendar-check' => 'calendar-check', 'bi-megaphone' => 'megaphone',
            'bi-chat-dots' => 'message-circle', 'bi-telephone' => 'phone', 'bi-phone' => 'smartphone', 'bi-truck' => 'truck', 'bi-tools' => 'wrench',
            'bi-shield-check' => 'shield-check', 'bi-search' => 'search', 'bi-building' => 'building-2', 'bi-globe2' => 'globe',
        ] as $lama => $baru) DB::table('layanans')->where('icon', $lama)->update(['icon' => $baru]);
    }

    public function down(): void {}
};
