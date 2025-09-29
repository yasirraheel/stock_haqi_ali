# AI-Powered Screenshot Generation

This implementation replaces the FFmpeg-based screenshot generation with Google's Gemini AI to create custom thumbnails based on video title and description.

## Features

- **AI-Generated Thumbnails**: Uses Google Gemini AI to create custom movie thumbnails based on video title and description
- **Fallback System**: If Gemini AI fails, automatically falls back to a simple text-based image generator
- **No FFmpeg Dependency**: Completely removes the need for FFmpeg installation and configuration
- **Error Handling**: Comprehensive error handling with detailed logging
- **Configurable**: Easy to configure via environment variables

## Setup

### 1. Environment Configuration

Make sure your `.env` file contains the Gemini API key:

```env
GEMINI_API_KEY=your_gemini_api_key_here
```

### 2. API Key Setup

1. Go to [Google AI Studio](https://aistudio.google.com/)
2. Create a new project or select an existing one
3. Generate an API key for the Gemini API
4. Add the API key to your `.env` file

### 3. Directory Permissions

Ensure the screenshots directory is writable:

```bash
chmod 755 public/screenshots
```

## How It Works

### 1. AI Image Generation Process

When a video is uploaded or updated:

1. **Smart Detection**: The system intelligently determines when to generate a new screenshot:
   - **New videos**: Always generate a new screenshot
   - **Updated videos**: Only generate if title, description, or file ID changed
   - **Missing thumbnails**: Generate if no existing thumbnail exists

2. **Prompt Creation**: The system creates a detailed prompt based on:
   - Video title
   - Video description
   - Genre information (if available)

3. **Gemini API Call**: Sends the prompt to Google's Gemini AI with:
   - 16:9 aspect ratio for video thumbnails
   - Safety settings to filter inappropriate content
   - High-quality image generation

4. **Image Processing**: 
   - Decodes the base64 image data from Gemini
   - Saves the image to `public/screenshots/` directory
   - Updates the database with the image path

### 2. Fallback System

If Gemini AI fails:

1. **Fallback Generation**: Creates a simple text-based image using PHP GD
2. **Gradient Background**: Generates an attractive gradient background
3. **Text Overlay**: Adds the video title and "Video Thumbnail" text
4. **Professional Look**: Maintains a professional appearance

## Code Structure

### New Files Created

- `app/Services/GeminiImageService.php` - Main service for AI image generation
- `test_gemini.php` - Test script to verify functionality

### Modified Files

- `app/Http/Controllers/Admin/MoviesController.php` - Updated to use AI generation with smart update detection
- `config/services.php` - Already configured for Gemini API

### New Methods Added

- `generateAIScreenshot()` - Main AI screenshot generation method
- `regenerateScreenshot($movie_id)` - Manual screenshot regeneration for existing videos

## API Integration

### GeminiImageService Methods

#### `generateImage($videoTitle, $videoDescription, $fileId)`
- Generates AI-powered image using Gemini
- Returns success/error array with image path

#### `generateFallbackImage($videoTitle, $fileId)`
- Creates fallback image using PHP GD
- Returns image path or null on failure

#### `createImagePrompt($videoTitle, $videoDescription)`
- Creates detailed prompt for AI generation
- Optimized for movie poster/thumbnail creation

## Configuration Options

### Safety Settings
The service includes comprehensive safety settings:
- Hate speech filtering
- Dangerous content filtering
- Sexually explicit content filtering
- Harassment filtering

### Image Specifications
- **Aspect Ratio**: 16:9 (optimized for video thumbnails)
- **Format**: JPEG
- **Quality**: High (90% compression)
- **Size**: 1280x720 pixels (fallback)

## Error Handling

The system includes multiple layers of error handling:

1. **API Errors**: Catches and logs Gemini API failures
2. **Network Issues**: Handles connection timeouts and network problems
3. **File System Errors**: Manages directory creation and file writing issues
4. **Fallback Activation**: Automatically switches to fallback on any failure

## Logging

All operations are logged with appropriate levels:
- `INFO`: Successful operations
- `WARNING`: Fallback activations
- `ERROR`: Critical failures

Logs are written to Laravel's default log files.

## Testing

Run the test script to verify functionality:

```bash
php test_gemini.php
```

This will:
1. Test Gemini AI image generation
2. Test fallback image generation
3. Verify file saving
4. Display success/error messages

## Troubleshooting

### Common Issues

1. **API Key Invalid**
   - Verify the API key in `.env`
   - Check API key permissions in Google AI Studio

2. **Directory Permissions**
   - Ensure `public/screenshots` is writable
   - Check file system permissions

3. **Network Issues**
   - Verify internet connectivity
   - Check firewall settings

4. **Memory Issues**
   - Increase PHP memory limit if needed
   - Monitor server resources

### Debug Mode

Enable detailed logging by setting:
```env
LOG_LEVEL=debug
```

## Performance Considerations

- **API Rate Limits**: Gemini API has rate limits; consider implementing queuing for high-volume usage
- **Caching**: Images are cached in the public directory to avoid regeneration
- **Memory Usage**: Fallback method uses PHP GD which requires more memory than FFmpeg

## Video Update Behavior

### Smart Screenshot Generation

The system intelligently handles video updates:

1. **New Videos**: Always generates a new AI-powered screenshot
2. **Updated Videos**: Only regenerates screenshot when:
   - Video title changes
   - Video description changes  
   - File ID changes (new video file)
   - No existing thumbnail found
3. **Unchanged Videos**: Preserves existing thumbnail to save API calls and processing time

### Manual Regeneration

Administrators can manually regenerate screenshots for any video using the `regenerateScreenshot()` method:

```php
// Route example (add to your routes file)
Route::post('/admin/movies/{id}/regenerate-screenshot', [MoviesController::class, 'regenerateScreenshot'])
    ->name('admin.movies.regenerate-screenshot');
```

## Migration from FFmpeg

The new system is a drop-in replacement:
- No changes needed to existing database structure
- Same file paths and naming convention
- Compatible with existing thumbnail display code
- Automatic fallback ensures reliability
- Smart update detection prevents unnecessary API calls

## Future Enhancements

Potential improvements:
- Batch processing for multiple videos
- Custom prompt templates per genre
- Image quality optimization
- Caching layer for API responses
- Queue-based processing for large uploads

## Support

For issues or questions:
1. Check the logs for detailed error messages
2. Verify API key configuration
3. Test with the provided test script
4. Review directory permissions
