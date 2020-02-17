package ml.banq.android;

import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.webkit.CookieManager;
import android.webkit.CookieSyncManager;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.webkit.WebResourceRequest;

public class CustomWebViewClient extends WebViewClient {
    Context context;

    CustomWebViewClient(Context context) {
        this.context = context;
    }

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
            context.startActivity(new Intent(Intent.ACTION_VIEW, uri));
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
}
