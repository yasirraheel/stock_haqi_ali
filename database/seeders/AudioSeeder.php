<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Audio;

class AudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $audioFiles = [
            [
                'title' => 'Experimental Cinematic Hip Hop',
                'description' => 'A dynamic blend of cinematic elements with hip hop beats, perfect for dramatic scenes and action sequences.',
                'audio_path' => 'audio/1758923239_experimental-cinematic-hip-hop-315904.mp3',
                'duration' => '3:45',
                'file_size' => '5.2 MB',
                'format' => 'mp3',
                'bitrate' => 320,
                'sample_rate' => 44100,
                'genre' => 'Hip Hop',
                'mood' => 'Dramatic',
                'tags' => 'cinematic,hip hop,experimental,action',
                'is_active' => true,
                'license_price' => 0.00,
                'downloads_count' => 0,
                'views_count' => 0
            ],
            [
                'title' => 'Deep Abstract Ambient',
                'description' => 'Atmospheric ambient soundscape with deep, meditative tones for relaxation and focus.',
                'audio_path' => 'audio/1758924172_deep-abstract-ambient_snowcap-401656.mp3',
                'duration' => '4:12',
                'file_size' => '6.1 MB',
                'format' => 'mp3',
                'bitrate' => 320,
                'sample_rate' => 44100,
                'genre' => 'Ambient',
                'mood' => 'Calm',
                'tags' => 'ambient,meditation,relaxation,atmospheric',
                'is_active' => true,
                'license_price' => 0.00,
                'downloads_count' => 0,
                'views_count' => 0
            ],
            [
                'title' => 'Electronic Digital Beat',
                'description' => 'Modern electronic track with digital elements, ideal for tech presentations and futuristic content.',
                'audio_path' => 'audio/1759008408_the-last-point-beat-electronic-digital-394291.mp3',
                'duration' => '3:28',
                'file_size' => '4.8 MB',
                'format' => 'mp3',
                'bitrate' => 320,
                'sample_rate' => 44100,
                'genre' => 'Electronic',
                'mood' => 'Energetic',
                'tags' => 'electronic,digital,tech,futuristic',
                'is_active' => true,
                'license_price' => 15.99,
                'downloads_count' => 0,
                'views_count' => 0
            ],
            [
                'title' => 'Can We Use 100% of our Brain',
                'description' => 'Thought-provoking track exploring the mysteries of human consciousness and potential.',
                'audio_path' => 'audio/1759165893_Can We Use 100 of our Brain..mp3',
                'duration' => '5:15',
                'file_size' => '7.5 MB',
                'format' => 'mp3',
                'bitrate' => 320,
                'sample_rate' => 44100,
                'genre' => 'Documentary',
                'mood' => 'Thoughtful',
                'tags' => 'documentary,consciousness,educational,thoughtful',
                'is_active' => true,
                'license_price' => 0.00,
                'downloads_count' => 0,
                'views_count' => 0
            ],
            [
                'title' => 'SSSTik Ambient Mix',
                'description' => 'Smooth ambient mix perfect for background music and relaxation sessions.',
                'audio_path' => 'audio/1758924351_ssstik.io_1748242247234.mp3',
                'duration' => '4:33',
                'file_size' => '6.3 MB',
                'format' => 'mp3',
                'bitrate' => 320,
                'sample_rate' => 44100,
                'genre' => 'Ambient',
                'mood' => 'Peaceful',
                'tags' => 'ambient,background,relaxation,smooth',
                'is_active' => true,
                'license_price' => 9.99,
                'downloads_count' => 0,
                'views_count' => 0
            ],
            [
                'title' => 'Deep Abstract Ambient 2',
                'description' => 'Another deep ambient track with abstract elements for meditation and focus.',
                'audio_path' => 'audio/1758924174_deep-abstract-ambient_snowcap-401656.mp3',
                'duration' => '4:12',
                'file_size' => '6.1 MB',
                'format' => 'mp3',
                'bitrate' => 320,
                'sample_rate' => 44100,
                'genre' => 'Ambient',
                'mood' => 'Meditative',
                'tags' => 'ambient,meditation,abstract,deep',
                'is_active' => true,
                'license_price' => 0.00,
                'downloads_count' => 0,
                'views_count' => 0
            ]
        ];

        foreach ($audioFiles as $audioData) {
            Audio::create($audioData);
        }
    }
}
