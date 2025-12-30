// settings.gradle.kts

pluginManagement {
    repositories {
        // HAPUS blok 'content {}' di sini untuk memastikan resolusi penuh
        google()
        mavenCentral()
        gradlePluginPortal()
    }
}
dependencyResolutionManagement {
    repositoriesMode.set(RepositoriesMode.FAIL_ON_PROJECT_REPOS)
    repositories {
        // PASTIKAN ada google() di sini
        google()
        mavenCentral()
    }
}

rootProject.name = "pet society"
include(":app")