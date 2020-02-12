package ml.banq.android;

import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.webkit.CookieManager;
import android.webkit.CookieSyncManager;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.webkit.WebResourceRequest;
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

        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true);

        webView.setWebViewClient(new WebViewClient() {
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                return handleUri(Uri.parse(url));
            }

            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                return handleUri(request.getUrl());
            }

            private boolean handleUri(Uri uri) {
                if (uri.getScheme().equals("https") && uri.getHost().equals("banq.ml")) {
                    return false;
                } else {
                    startActivity(new Intent(Intent.ACTION_VIEW, uri));
                    return true;
                }
            }

            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                if (Build.VERSION.SDK_INT >= 21) {
                    CookieManager.getInstance().flush();
                } else {
                    CookieSyncManager.getInstance().sync();
                }
            }
        });

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
