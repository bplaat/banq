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

        if (Build.VERSION.SDK_INT < 21) {
            CookieSyncManager.createInstance(this);
        }

        webView = new WebView(this);
        setContentView(webView);

        webView.addJavascriptInterface(new WebAppInterface(this), "Android");

        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true);

        webView.setWebViewClient(new CustomWebViewClient(this));

        Intent intent = getIntent();
        if (intent.getAction() == Intent.ACTION_VIEW) {
            webView.loadUrl(intent.getDataString());
        } else {
            webView.loadUrl("https://banq.ml/");
        }
    }

    public void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        if (intent.getAction() == Intent.ACTION_VIEW) {
            webView.loadUrl(intent.getDataString());
        }
    }

    public void onPause() {
        super.onPause();
        webView.clearHistory();
    }

    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            super.onBackPressed();
        }
    }
}
