/****************************************************************************
 * *
 * Copyright (C) 2014-2015 iBuildApp, Inc. ( http://ibuildapp.com )         *
 * *
 * This file is part of iBuildApp.                                          *
 * *
 * This Source Code Form is subject to the terms of the iBuildApp License.  *
 * You can obtain one at http://ibuildapp.com/license/                      *
 * *
 ****************************************************************************/
package com.ibuildapp.romanblack.WebPlugin;

import android.sax.EndElementListener;
import android.sax.EndTextElementListener;
import android.sax.RootElement;
import android.sax.StartElementListener;
import android.util.Log;
import android.util.Xml;

import org.xml.sax.Attributes;

import java.io.ByteArrayInputStream;

/**
 * This class using for module xml data parsing.
 */
public class EntityParser {

    private String xml = "";
    private String title = "";
    private String url = "";
    private String html = "";

    public String getPluginData() {
        return pluginData;
    }

    public void setPluginData(String pluginData) {
        this.pluginData = pluginData;
    }

    private String pluginData = "";

    /**
     * Constructs new EntityParser instance
     *
     * @param xml - module xml data to parse
     */
    EntityParser(String xml) {
        this.xml = xml.replaceAll("\u000b", "");
    }

    /**
     * @return parsed title
     */
    public String getTitle() {
        return title;
    }

    /**
     * @return html to load in WebView
     */
    public String getHtml() {
        return html;
    }

    /**
     * @return url to load in WebView
     */
    public String getUrl() {
        return url;
    }

    /**
     * Parse module data that was set in constructor
     */
    public void parse() {
        RootElement root = new RootElement("data");
        android.sax.Element title = root.getChild("title");
        android.sax.Element content = root.getChild("content");
        android.sax.Element plugins = root.getChild("plugins");

        root.setEndElementListener(new EndElementListener() {
            @Override
            public void end() {
            }
        });

        title.setEndTextElementListener(new EndTextElementListener() {
            @Override
            public void end(String body) {
                EntityParser.this.title = body;
            }
        });

        content.setStartElementListener(new StartElementListener() {
            @Override
            public void start(Attributes attributes) {
                if (attributes.getValue("src") != null) {
                    url = attributes.getValue("src");
                }
            }
        });

        content.setEndTextElementListener(new EndTextElementListener() {
            @Override
            public void end(String body) {
                html = body;
            }
        });

        plugins.setEndTextElementListener(new EndTextElementListener() {
            @Override
            public void end(String body) {
                setPluginData(body);

            }
        });

        try {
            Xml.parse(new ByteArrayInputStream(xml.getBytes()), Xml.Encoding.UTF_8, root.getContentHandler());
        } catch (Exception e) {
            Log.d("", "");
        }
    }
}
