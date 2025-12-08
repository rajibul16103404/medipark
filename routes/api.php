<?php

use App\Http\Controllers\Api\AboutUsPage2ndAfterOurVisionSectionController;
use App\Http\Controllers\Api\AboutUsPageAfterOurVisionSectionController;
use App\Http\Controllers\Api\AboutUsPageBannerSectionController;
use App\Http\Controllers\Api\AboutUsPageOurMissionSectionController;
use App\Http\Controllers\Api\AboutUsPageOurVisionSectionController;
use App\Http\Controllers\Api\AboutUsPageWhoWeAreSectionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\HomepageAboutUsSectionController;
use App\Http\Controllers\Api\HomepageCtaSectionController;
use App\Http\Controllers\Api\HomepageHeroSectionController;
use App\Http\Controllers\Api\InvestorController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\PrivilegeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Password reset routes (public)
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    // OTP routes (public)
    Route::post('/send-otp', [OtpController::class, 'sendOtp']);
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);

    Route::post('/resend-verification-email', [AuthController::class, 'resendVerificationEmail']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

    // Investor login routes (public)
    Route::post('/investor/request-login', [AuthController::class, 'requestInvestorLogin']);
    Route::post('/investor/login', [AuthController::class, 'investorLogin']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // Investor authenticated routes
    Route::middleware('auth.investor')->prefix('investor')->group(function () {
        Route::get('/me', [AuthController::class, 'investorMe']);
        Route::post('/logout', [AuthController::class, 'investorLogout']);
        Route::post('/refresh', [AuthController::class, 'investorRefresh']);
    });
});

// Public Routes
Route::get('/homepage-hero-sections/active', [HomepageHeroSectionController::class, 'show']);
Route::get('/homepage-about-us-sections/active', [HomepageAboutUsSectionController::class, 'show']);
Route::get('/homepage-cta-sections/active', [HomepageCtaSectionController::class, 'show']);
Route::get('/about-us-page-banner-sections/active', [AboutUsPageBannerSectionController::class, 'show']);
Route::get('/about-us-page-who-we-are-sections/active', [AboutUsPageWhoWeAreSectionController::class, 'show']);
Route::get('/about-us-page-our-mission-sections/active', [AboutUsPageOurMissionSectionController::class, 'show']);
Route::get('/about-us-page-our-vision-sections/active', [AboutUsPageOurVisionSectionController::class, 'show']);
Route::get('/about-us-page-after-our-vision-sections/active', [AboutUsPageAfterOurVisionSectionController::class, 'show']);
Route::get('/about-us-page-2nd-after-our-vision-sections/active', [AboutUsPage2ndAfterOurVisionSectionController::class, 'show']);

// Contact routes (Public submission)
Route::post('/contacts', [ContactController::class, 'store']);

Route::middleware('auth:api')->group(function () {
    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->middleware('privilege:read-profile');
        Route::put('/', [ProfileController::class, 'update'])->middleware('privilege:update-profile');
    });

    // Privilege routes
    Route::prefix('privileges')->group(function () {
        Route::get('/', [PrivilegeController::class, 'index'])->middleware('privilege:read-privileges,read-roles,assign-privileges-to-roles');
        Route::get('/{privilege}', [PrivilegeController::class, 'show'])->middleware('privilege:read-privileges,read-roles,assign-privileges-to-roles');
    });

    // Role routes
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('privilege:read-roles,assign-privileges-to-roles');
        Route::get('/{role}', [RoleController::class, 'show'])->middleware('privilege:read-roles,assign-privileges-to-roles');
        Route::post('/', [RoleController::class, 'store'])->middleware('privilege:create-roles');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('privilege:update-roles');
        Route::patch('/{role}', [RoleController::class, 'update'])->middleware('privilege:update-roles');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('privilege:delete-roles');
        Route::post('/{role}/privileges', [RoleController::class, 'assignPrivileges'])->middleware('privilege:assign-privileges-to-roles');
        Route::delete('/{role}/privileges', [RoleController::class, 'removePrivileges'])->middleware('privilege:assign-privileges-to-roles');
        Route::delete('/{role}/privileges/{privilege}', [RoleController::class, 'removePrivilege'])->middleware('privilege:assign-privileges-to-roles');
    });

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('privilege:read-users');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('privilege:read-users');
        Route::post('/', [UserController::class, 'store'])->middleware('privilege:create-users');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('privilege:update-users');
        Route::patch('/{user}', [UserController::class, 'update'])->middleware('privilege:update-users');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('privilege:delete-users');
        Route::post('/{user}/suspend', [UserController::class, 'suspend'])->middleware('privilege:suspend-users');
        Route::post('/{user}/unsuspend', [UserController::class, 'unsuspend'])->middleware('privilege:suspend-users');
    });

    // Homepage Hero Section routes (Admin)
    Route::prefix('homepage-hero-sections')->group(function () {
        Route::get('/', [HomepageHeroSectionController::class, 'index'])->middleware('privilege:read-homepage-hero-sections');
        Route::get('/{homepageHeroSection}', [HomepageHeroSectionController::class, 'showById'])->middleware('privilege:read-homepage-hero-sections');
        Route::post('/', [HomepageHeroSectionController::class, 'store'])->middleware('privilege:create-homepage-hero-sections');
        Route::post('/{homepageHeroSection}', [HomepageHeroSectionController::class, 'update'])->middleware('privilege:update-homepage-hero-sections');
        Route::delete('/{homepageHeroSection}', [HomepageHeroSectionController::class, 'destroy'])->middleware('privilege:delete-homepage-hero-sections');
        Route::post('/{homepageHeroSection}/set-active', [HomepageHeroSectionController::class, 'setActive'])->middleware('privilege:update-homepage-hero-sections');
    });

    // Homepage About Us Section routes (Admin)
    Route::prefix('homepage-about-us-sections')->group(function () {
        Route::get('/', [HomepageAboutUsSectionController::class, 'index'])->middleware('privilege:read-homepage-about-us-sections');
        Route::get('/{homepageAboutUsSection}', [HomepageAboutUsSectionController::class, 'showById'])->middleware('privilege:read-homepage-about-us-sections');
        Route::post('/', [HomepageAboutUsSectionController::class, 'store'])->middleware('privilege:create-homepage-about-us-sections');
        Route::post('/{homepageAboutUsSection}', [HomepageAboutUsSectionController::class, 'update'])->middleware('privilege:update-homepage-about-us-sections');
        Route::delete('/{homepageAboutUsSection}', [HomepageAboutUsSectionController::class, 'destroy'])->middleware('privilege:delete-homepage-about-us-sections');
        Route::post('/{homepageAboutUsSection}/set-active', [HomepageAboutUsSectionController::class, 'setActive'])->middleware('privilege:update-homepage-about-us-sections');
    });

    // Homepage CTA Section routes (Admin)
    Route::prefix('homepage-cta-sections')->group(function () {
        Route::get('/', [HomepageCtaSectionController::class, 'index'])->middleware('privilege:read-homepage-cta-sections');
        Route::get('/{homepageCtaSection}', [HomepageCtaSectionController::class, 'showById'])->middleware('privilege:read-homepage-cta-sections');
        Route::post('/', [HomepageCtaSectionController::class, 'store'])->middleware('privilege:create-homepage-cta-sections');
        Route::put('/{homepageCtaSection}', [HomepageCtaSectionController::class, 'update'])->middleware('privilege:update-homepage-cta-sections');
        Route::delete('/{homepageCtaSection}', [HomepageCtaSectionController::class, 'destroy'])->middleware('privilege:delete-homepage-cta-sections');
        Route::post('/{homepageCtaSection}/set-active', [HomepageCtaSectionController::class, 'setActive'])->middleware('privilege:update-homepage-cta-sections');
    });

    // About Us Page Banner Section routes (Admin)
    Route::prefix('about-us-page-banner-sections')->group(function () {
        Route::get('/', [AboutUsPageBannerSectionController::class, 'index'])->middleware('privilege:read-about-us-page-banner-sections');
        Route::get('/{aboutUsPageBannerSection}', [AboutUsPageBannerSectionController::class, 'showById'])->middleware('privilege:read-about-us-page-banner-sections');
        Route::post('/', [AboutUsPageBannerSectionController::class, 'store'])->middleware('privilege:create-about-us-page-banner-sections');
        Route::put('/{aboutUsPageBannerSection}', [AboutUsPageBannerSectionController::class, 'update'])->middleware('privilege:update-about-us-page-banner-sections');
        Route::patch('/{aboutUsPageBannerSection}', [AboutUsPageBannerSectionController::class, 'update'])->middleware('privilege:update-about-us-page-banner-sections');
        Route::delete('/{aboutUsPageBannerSection}', [AboutUsPageBannerSectionController::class, 'destroy'])->middleware('privilege:delete-about-us-page-banner-sections');
        Route::post('/{aboutUsPageBannerSection}/set-active', [AboutUsPageBannerSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-banner-sections');
    });

    // About Us Page Who We Are Section routes (Admin)
    Route::prefix('about-us-page-who-we-are-sections')->group(function () {
        Route::get('/', [AboutUsPageWhoWeAreSectionController::class, 'index'])->middleware('privilege:read-about-us-page-who-we-are-sections');
        Route::get('/{aboutUsPageWhoWeAreSection}', [AboutUsPageWhoWeAreSectionController::class, 'showById'])->middleware('privilege:read-about-us-page-who-we-are-sections');
        Route::post('/', [AboutUsPageWhoWeAreSectionController::class, 'store'])->middleware('privilege:create-about-us-page-who-we-are-sections');
        Route::post('/{aboutUsPageWhoWeAreSection}', [AboutUsPageWhoWeAreSectionController::class, 'update'])->middleware('privilege:update-about-us-page-who-we-are-sections');
        Route::patch('/{aboutUsPageWhoWeAreSection}', [AboutUsPageWhoWeAreSectionController::class, 'update'])->middleware('privilege:update-about-us-page-who-we-are-sections');
        Route::delete('/{aboutUsPageWhoWeAreSection}', [AboutUsPageWhoWeAreSectionController::class, 'destroy'])->middleware('privilege:delete-about-us-page-who-we-are-sections');
        Route::post('/{aboutUsPageWhoWeAreSection}/set-active', [AboutUsPageWhoWeAreSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-who-we-are-sections');
    });

    // About Us Page Our Mission Section routes (Admin)
    Route::prefix('about-us-page-our-mission-sections')->group(function () {
        Route::get('/', [AboutUsPageOurMissionSectionController::class, 'index'])->middleware('privilege:read-about-us-page-our-mission-sections');
        Route::get('/{aboutUsPageOurMissionSection}', [AboutUsPageOurMissionSectionController::class, 'showById'])->middleware('privilege:read-about-us-page-our-mission-sections');
        Route::post('/', [AboutUsPageOurMissionSectionController::class, 'store'])->middleware('privilege:create-about-us-page-our-mission-sections');
        Route::post('/{aboutUsPageOurMissionSection}', [AboutUsPageOurMissionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-our-mission-sections');
        Route::patch('/{aboutUsPageOurMissionSection}', [AboutUsPageOurMissionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-our-mission-sections');
        Route::delete('/{aboutUsPageOurMissionSection}', [AboutUsPageOurMissionSectionController::class, 'destroy'])->middleware('privilege:delete-about-us-page-our-mission-sections');
        Route::post('/{aboutUsPageOurMissionSection}/set-active', [AboutUsPageOurMissionSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-our-mission-sections');
    });

    // About Us Page Our Vision Section routes (Admin)
    Route::prefix('about-us-page-our-vision-sections')->group(function () {
        Route::get('/', [AboutUsPageOurVisionSectionController::class, 'index'])->middleware('privilege:read-about-us-page-our-vision-sections');
        Route::get('/{aboutUsPageOurVisionSection}', [AboutUsPageOurVisionSectionController::class, 'showById'])->middleware('privilege:read-about-us-page-our-vision-sections');
        Route::post('/', [AboutUsPageOurVisionSectionController::class, 'store'])->middleware('privilege:create-about-us-page-our-vision-sections');
        Route::post('/{aboutUsPageOurVisionSection}', [AboutUsPageOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-our-vision-sections');
        Route::patch('/{aboutUsPageOurVisionSection}', [AboutUsPageOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-our-vision-sections');
        Route::delete('/{aboutUsPageOurVisionSection}', [AboutUsPageOurVisionSectionController::class, 'destroy'])->middleware('privilege:delete-about-us-page-our-vision-sections');
        Route::post('/{aboutUsPageOurVisionSection}/set-active', [AboutUsPageOurVisionSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-our-vision-sections');
    });

    // About Us Page After Our Vision Section routes (Admin)
    Route::prefix('about-us-page-after-our-vision-sections')->group(function () {
        Route::get('/', [AboutUsPageAfterOurVisionSectionController::class, 'index'])->middleware('privilege:read-about-us-page-after-our-vision-sections');
        Route::get('/{aboutUsPageAfterOurVisionSection}', [AboutUsPageAfterOurVisionSectionController::class, 'showById'])->middleware('privilege:read-about-us-page-after-our-vision-sections');
        Route::post('/', [AboutUsPageAfterOurVisionSectionController::class, 'store'])->middleware('privilege:create-about-us-page-after-our-vision-sections');
        Route::post('/{aboutUsPageAfterOurVisionSection}', [AboutUsPageAfterOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-after-our-vision-sections');
        Route::patch('/{aboutUsPageAfterOurVisionSection}', [AboutUsPageAfterOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-after-our-vision-sections');
        Route::delete('/{aboutUsPageAfterOurVisionSection}', [AboutUsPageAfterOurVisionSectionController::class, 'destroy'])->middleware('privilege:delete-about-us-page-after-our-vision-sections');
        Route::post('/{aboutUsPageAfterOurVisionSection}/set-active', [AboutUsPageAfterOurVisionSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-after-our-vision-sections');
    });

    // About Us Page 2nd After Our Vision Section routes (Admin)
    Route::prefix('about-us-page-2nd-after-our-vision-sections')->group(function () {
        Route::get('/', [AboutUsPage2ndAfterOurVisionSectionController::class, 'index'])->middleware('privilege:read-about-us-page-2nd-after-our-vision-sections');
        Route::get('/{section}', [AboutUsPage2ndAfterOurVisionSectionController::class, 'showById'])->middleware('privilege:read-about-us-page-2nd-after-our-vision-sections');
        Route::post('/', [AboutUsPage2ndAfterOurVisionSectionController::class, 'store'])->middleware('privilege:create-about-us-page-2nd-after-our-vision-sections');
        Route::post('/{section}', [AboutUsPage2ndAfterOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-2nd-after-our-vision-sections');
        Route::patch('/{section}', [AboutUsPage2ndAfterOurVisionSectionController::class, 'update'])->middleware('privilege:update-about-us-page-2nd-after-our-vision-sections');
        Route::delete('/{section}', [AboutUsPage2ndAfterOurVisionSectionController::class, 'destroy'])->middleware('privilege:delete-about-us-page-2nd-after-our-vision-sections');
        Route::post('/{section}/set-active', [AboutUsPage2ndAfterOurVisionSectionController::class, 'setActive'])->middleware('privilege:update-about-us-page-2nd-after-our-vision-sections');
    });

    // Contact routes (Admin)
    Route::prefix('contacts')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->middleware('privilege:read-contacts');
        Route::get('/{contact}', [ContactController::class, 'show'])->middleware('privilege:read-contacts');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->middleware('privilege:delete-contacts');
    });

    // Investor routes (Admin)
    Route::prefix('investors')->group(function () {
        Route::post('/', [InvestorController::class, 'store'])->middleware('privilege:create-investors');
        Route::get('/', [InvestorController::class, 'index'])->middleware('privilege:read-investors');
        Route::get('/{investor}', [InvestorController::class, 'show'])->middleware('privilege:read-investors');
        Route::put('/{investor}', [InvestorController::class, 'update'])->middleware('privilege:update-investors');
        Route::patch('/{investor}', [InvestorController::class, 'update'])->middleware('privilege:update-investors');
        Route::delete('/{investor}', [InvestorController::class, 'destroy'])->middleware('privilege:delete-investors');
    });

    // Doctor routes (Admin)
    Route::prefix('doctors')->group(function () {
        Route::get('/', [DoctorController::class, 'index'])->middleware('privilege:read-doctors');
        Route::get('/{doctor}', [DoctorController::class, 'show'])->middleware('privilege:read-doctors');
        Route::post('/', [DoctorController::class, 'store'])->middleware('privilege:create-doctors');
        Route::put('/{doctor}', [DoctorController::class, 'update'])->middleware('privilege:update-doctors');
        Route::patch('/{doctor}', [DoctorController::class, 'update'])->middleware('privilege:update-doctors');
        Route::delete('/{doctor}', [DoctorController::class, 'destroy'])->middleware('privilege:delete-doctors');
    });
});
