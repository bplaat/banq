package ml.banq.android;

import android.app.Activity;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.webkit.CookieSyncManager;
import android.webkit.WebView;
import android.webkit.WebSettings;

public class MainActivity extends Activity {
    private WebView webView;

    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Create a cookie sync instance for older Android versions
        if (Build.VERSION.SDK_INT < 21) {
            CookieSyncManager.createInstance(this);
        }

        // Create a new webview view and make it the primary view of the activity
        webView = new WebView(this);
        setContentView(webView);

        // Add the custom web app interface to the webview and make it available at the Android variable
        webView.addJavascriptInterface(new WebAppInterface(this), "Android");

        // Enable Javascript in the webview
        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true);

        // Add the custom web view client event handler
        webView.setWebViewClient(new CustomWebViewClient(this));

        // Check the action if the intent which created this activity is a view action then open to corresponding url
        Intent intent = getIntent();
        if (intent.getAction() == Intent.ACTION_VIEW) {
            webView.loadUrl(intent.getDataString());
        }

        // Else open the Banq homepage
        else {
            webView.loadUrl("https://banq.ml/");
        }
    }

    // Listen for incoming Intents if the action is a view action open the corresponding url
    public void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        if (intent.getAction() == Intent.ACTION_VIEW) {
            webView.loadUrl(intent.getDataString());
        }
    }

    // When the app is paused clear the history of the webview
    public void onPause() {
        super.onPause();
        webView.clearHistory();
    }

    // When a user pressed the back button check if the webview can go back then go back
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            super.onBackPressed();
        }
    }
}
