<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('homepages', function (Blueprint $table) {
            $table->id();

            // Hero Section
            $table->string('hero_title')->nullable();
            $table->string('hero_subtitle')->nullable();
            $table->string('hero_background_image')->nullable();

            // About Us Section
            $table->string('about_title')->nullable();
            $table->text('about_content')->nullable();
            $table->json('about_images')->nullable(); // Array of image URLs

            // Our Doctors Section
            $table->string('doctors_title')->nullable();
            $table->text('doctors_description')->nullable();

            // Pricing Section
            $table->string('pricing_title')->nullable();
            $table->text('pricing_description')->nullable();

            // News & Media Section
            $table->string('news_title')->nullable();
            $table->text('news_description')->nullable();

            // Ask Medipark Section
            $table->string('ask_title')->nullable();
            $table->string('ask_subtitle')->nullable();
            $table->string('ask_button_text')->default('Send Query');

            // Blog Section
            $table->string('blog_title')->nullable();
            $table->text('blog_description')->nullable();

            // Investor Section
            $table->string('investor_title')->nullable();
            $table->text('investor_description')->nullable();

            // Footer Section
            $table->json('footer_contact')->nullable(); // {phone, email, address}
            $table->json('footer_links')->nullable(); // {services: [], about: [], faqs: []}
            $table->json('footer_social_links')->nullable(); // {facebook, twitter, youtube, linkedin}
            $table->string('footer_copyright')->nullable();

            // Meta
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepages');
    }
};
