/**
 * Google Apps Script for Google Form to Laravel Integration
 * 
 * INSTRUCTIONS:
 * 1. Open your Google Form
 * 2. Click the three dots (⋮) in the top right corner
 * 3. Select "Script editor"
 * 4. Paste this entire code into the script editor
 * 5. Replace 'YOUR_LARAVEL_URL' with your actual Laravel project URL (e.g., 'https://myproject.com')
 * 6. Save the script (Ctrl+S or Cmd+S)
 * 7. Click on "Triggers" in the left sidebar (clock icon)
 * 8. Click "+ Add Trigger" button
 * 9. Configure the trigger:
 *    - Choose which function to run: onFormSubmit
 *    - Select event source: From form
 *    - Select event type: On form submit
 * 10. Click "Save"
 * 11. Authorize the script when prompted (first time only)
 * 
 * The script will automatically send form submissions to your Laravel endpoint.
 */

// CONFIGURATION: Replace with your Laravel project URL
const LARAVEL_ENDPOINT = 'https://crm-demo.test/api/google-form-response';

/**
 * Main function triggered when Google Form is submitted
 * @param {GoogleAppsScript.Events.FormsOnFormSubmitEvent} e - Form submission event
 */
function onFormSubmit(e) {
  try {
    // Get the form response
    const formResponse = e.response;
    const form = e.source;
    
    // Log the submission start
    Logger.log('=== Google Form Submission Started ===');
    Logger.log('Timestamp: ' + new Date().toISOString());
    Logger.log('Form ID: ' + form.getId());
    
    // Collect all form responses as key-value pairs
    const formData = {};
    const itemResponses = formResponse.getItemResponses();
    
    // Process each form item
    itemResponses.forEach(function(itemResponse) {
      const item = itemResponse.getItem();
      const question = item.getTitle();
      const answer = itemResponse.getResponse();
      
      // Store as key-value pair
      formData[question] = answer;
      
      // Log each question-answer pair
      Logger.log('Q: ' + question + ' | A: ' + answer);
    });
    
    // Add metadata
    formData['_metadata'] = {
      'form_id': form.getId(),
      'form_title': form.getTitle(),
      'response_id': formResponse.getId(),
      'timestamp': formResponse.getTimestamp().toISOString(),
      'respondent_email': formResponse.getRespondentEmail() || 'Not provided'
    };
    
    // Log the complete data object
    Logger.log('Complete Form Data: ' + JSON.stringify(formData, null, 2));
    
    // Send data to Laravel endpoint
    const response = sendToLaravel(formData);
    
    // Log the response
    Logger.log('Laravel Response Status: ' + response.getResponseCode());
    Logger.log('Laravel Response: ' + response.getContentText());
    
    if (response.getResponseCode() === 200) {
      Logger.log('✓ Successfully sent form data to Laravel');
    } else {
      Logger.log('✗ Error: Failed to send data to Laravel. Status: ' + response.getResponseCode());
    }
    
    Logger.log('=== Google Form Submission Completed ===');
    
  } catch (error) {
    // Log any errors
    Logger.log('ERROR in onFormSubmit: ' + error.toString());
    Logger.log('Error Stack: ' + error.stack);
    
    // Optionally, you can send error notification via email
    // MailApp.sendEmail({
    //   to: 'your-email@example.com',
    //   subject: 'Google Form Script Error',
    //   body: 'An error occurred: ' + error.toString()
    // });
  }
}

/**
 * Send form data to Laravel endpoint via POST request
 * @param {Object} formData - The form data to send
 * @return {GoogleAppsScript.URL_Fetch.HTTPResponse} - HTTP response from Laravel
 */
function sendToLaravel(formData) {
  try {
    const payload = JSON.stringify(formData);
    
    const options = {
      'method': 'post',
      'contentType': 'application/json',
      'payload': payload,
      'muteHttpExceptions': true // Don't throw exceptions for HTTP error codes
    };
    
    Logger.log('Sending POST request to: ' + LARAVEL_ENDPOINT);
    Logger.log('Payload: ' + payload);
    
    const response = UrlFetchApp.fetch(LARAVEL_ENDPOINT, options);
    
    return response;
    
  } catch (error) {
    Logger.log('ERROR in sendToLaravel: ' + error.toString());
    Logger.log('Error Stack: ' + error.stack);
    throw error;
  }
}

/**
 * Test function to verify the script is working
 * Run this manually from the script editor to test the connection
 */
function testConnection() {
  const testData = {
    'test_question': 'This is a test submission',
    '_metadata': {
      'form_id': 'test_form',
      'form_title': 'Test Form',
      'response_id': 'test_response',
      'timestamp': new Date().toISOString(),
      'respondent_email': 'test@example.com'
    }
  };
  
  Logger.log('Testing connection to Laravel...');
  const response = sendToLaravel(testData);
  Logger.log('Test Response Status: ' + response.getResponseCode());
  Logger.log('Test Response: ' + response.getContentText());
}

