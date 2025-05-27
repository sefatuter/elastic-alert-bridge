<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Views - Elasticsearch</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f7f9fb;
            color: #343741;
            font-size: 14px;
            overflow: hidden;
        }

        .icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        .icon-discover { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23006bb4'%3E%3Cpath d='M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z'/%3E%3C/svg%3E"); 
        }

        .icon-calendar { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23343741'%3E%3Cpath d='M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z'/%3E%3C/svg%3E"); 
        }

        .icon-refresh { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23006bb4'%3E%3Cpath d='M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z'/%3E%3C/svg%3E"); 
        }

        .icon-arrow-down { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23343741'%3E%3Cpath d='M7,10L12,15L17,10H7Z'/%3E%3C/svg%3E"); 
        }

        .icon-arrow-right { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23006bb4'%3E%3Cpath d='M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z'/%3E%3C/svg%3E"); 
        }

        .icon-field-text { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 14 14'%3E%3Crect width='14' height='14' rx='2' fill='%23e7f3ff'/%3E%3Ctext x='7' y='10' text-anchor='middle' font-family='monospace' font-size='8' fill='%23006bb4'%3Et%3C/text%3E%3C/svg%3E"); 
        }

        .icon-field-number { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 14 14'%3E%3Crect width='14' height='14' rx='2' fill='%23e6f7ff'/%3E%3Ctext x='7' y='10' text-anchor='middle' font-family='monospace' font-size='8' fill='%230079a5'%3E%23%3C/text%3E%3C/svg%3E"); 
        }

        .icon-field-date { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 14 14'%3E%3Crect width='14' height='14' rx='2' fill='%23f0f9e7'/%3E%3Ctext x='7' y='10' text-anchor='middle' font-family='monospace' font-size='7' fill='%235cb85c'%3E@%3C/text%3E%3C/svg%3E"); 
        }

        .icon-field-geo { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 14 14'%3E%3Crect width='14' height='14' rx='2' fill='%23fff2e6'/%3E%3Ctext x='7' y='10' text-anchor='middle' font-family='monospace' font-size='8' fill='%23ff7f00'%3Eg%3C/text%3E%3C/svg%3E"); 
        }

        .icon-sort { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23343741'%3E%3Cpath d='M18 21L14 17H17V7H14L18 3L22 7H19V17H22M2 19V17H12V19M2 13V11H9V13M2 7V5H6V7H2Z'/%3E%3C/svg%3E"); 
        }

        .icon-chart { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23343741'%3E%3Cpath d='M22,21H2V3H4V19H6V17H10V19H12V16H16V19H18V17H22V21Z'/%3E%3C/svg%3E"); 
        }

        .icon-table { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23343741'%3E%3Cpath d='M5,4H19A2,2 0 0,1 21,6V18A2,2 0 0,1 19,20H5A2,2 0 0,1 3,18V6A2,2 0 0,1 5,4M5,8V12H11V8H5M13,8V12H19V8H13M5,14V18H11V14H5M13,14V18H19V14H13Z'/%3E%3C/svg%3E"); 
        }

        .icon-settings { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23343741'%3E%3Cpath d='M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z'/%3E%3C/svg%3E"); 
        }

        .icon-search { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23343741'%3E%3Cpath d='M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z'/%3E%3C/svg%3E"); 
        }

        .icon-add { 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ffffff'%3E%3Cpath d='M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z'/%3E%3C/svg%3E"); 
        }

        .icon-rules { /* Basic icon for Rules button */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ffffff'%3E%3Cpath d='M3 13H11V11H3M3 6V8H21V6M3 18V16H21V18Z'/%3E%3C/svg%3E");
        }

        .icon-integrations { /* Icon for Integrations button */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ffffff'%3E%3Cpath d='M12,2A2,2 0 0,1 14,4C14,4.74 13.6,5.39 13,5.73V7H14A7,7 0 0,1 21,14H22A1,1 0 0,1 23,15V18A1,1 0 0,1 22,19H21V20A2,2 0 0,1 19,22H5A2,2 0 0,1 3,20V19H2A1,1 0 0,1 1,18V15A1,1 0 0,1 2,14H3A7,7 0 0,1 10,7H11V5.73C10.4,5.39 10,4.74 10,4A2,2 0 0,1 12,2M7.5,13A2.5,2.5 0 0,0 5,15.5A2.5,2.5 0 0,0 7.5,18A2.5,2.5 0 0,0 10,15.5A2.5,2.5 0 0,0 7.5,13M16.5,13A2.5,2.5 0 0,0 14,15.5A2.5,2.5 0 0,0 16.5,18A2.5,2.5 0 0,0 19,15.5A2.5,2.5 0 0,0 16.5,13Z'/%3E%3C/svg%3E");
        }

        .kibana-header {
            background: #ffffff;
            border-bottom: 1px solid #d3dae6;
            height: 56px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
        }

        .kibana-logo {
            font-weight: 600;
            color: #006bb4;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .data-view-selector {
            position: relative;
            display: inline-block;
        }

        .data-view-dropdown {
            position: relative;
            background: #ffffff;
            border: 1px solid #d3dae6;
            border-radius: 6px;
            padding: 9px 32px 9px 12px;
            font-size: 14px;
            color: #343741;
            cursor: pointer;
            min-width: 200px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            height: 36px;
            font-weight: 500;
        }

        .data-view-dropdown:hover {
            border-color: #006bb4;
            box-shadow: 0 2px 4px rgba(0, 107, 180, 0.1);
        }

        .data-view-dropdown.open {
            border-color: #006bb4;
            box-shadow: 0 0 0 2px rgba(0, 107, 180, 0.1);
        }

        .data-view-dropdown::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #69707d;
            transition: transform 0.2s ease;
        }

        .data-view-dropdown.open::after {
            transform: translateY(-50%) rotate(180deg);
        }

        .data-view-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #d3dae6;
            border-top: none;
            border-radius: 0 0 6px 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
            display: none;
        }

        .data-view-options.show {
            display: block;
        }

        .data-view-option {
            padding: 8px 12px;
            font-size: 14px;
            color: #343741;
            cursor: pointer;
            border-bottom: 1px solid #f0f2f5;
            transition: background-color 0.1s ease;
        }

        .data-view-option:last-child {
            border-bottom: none;
        }

        .data-view-option:hover {
            background: #f7f9fb;
        }

        .data-view-option.selected {
            background: #e7f3ff;
            color: #006bb4;
            font-weight: 500;
        }

        .data-view-header {
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #69707d;
            background: #f7f9fb;
            border-bottom: 1px solid #d3dae6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .time-picker {
            display: flex;
            align-items: center;
            background: #f7f9fb;
            border: 1px solid #d3dae6;
            border-radius: 6px;
            padding: 9px 12px;
            font-size: 14px;
            gap: 8px;
            font-weight: 500;
            color: #343741;
            height: 36px;
            white-space: nowrap;
        }

        .refresh-btn {
            background: #ffffff;
            color: #006bb4;
            border: 1px solid #d3dae6;
            border-radius: 6px;
            padding: 9px 12px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            height: 36px;
            transition: all 0.2s ease;
        }

        .refresh-btn:hover {
            background-color: #f0f2f5;
            border-color: #98a2b3;
        }
        
        .action-btn {
            background: #006bb4;
            color: #ffffff;
            border: 1px solid #006bb4;
            border-radius: 6px;
            padding: 0 16px; /* Adjusted padding for icon and text */
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex; /* Use inline-flex for icon alignment */
            align-items: center;
            gap: 8px; /* Space between icon and text */
            height: 36px;
            transition: all 0.2s ease;
            text-decoration: none; /* For <a> tag */
        }

        .action-btn:hover {
            background: #005a9e;
            border-color: #005a9e;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .action-btn .icon {
            filter: brightness(0) invert(1); /* Ensures icon is white if it's not already */
        }

        .main-container {
            display: flex;
            height: 100vh;
            padding-top: 56px;
        }

        .sidebar {
            width: 240px;
            background: #ffffff;
            border-right: 1px solid #d3dae6;
            overflow-y: auto;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }

        .search-bar {
            padding: 12px;
            border-bottom: 1px solid #d3dae6;
        }

        .search-input {
            width: 100%;
            padding: 6px 28px 6px 8px;
            border: 1px solid #d3dae6;
            border-radius: 4px;
            font-size: 13px;
            position: relative;
            background: #ffffff;
            color: #343741;
        }

        .search-container {
            position: relative;
        }

        .search-icon {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #69707d;
            width: 14px;
            height: 14px;
        }

        .field-sections-container {
            flex: 1;
            overflow-y: auto;
        }

        .field-section {
            border-bottom: 1px solid #d3dae6;
        }

        .field-section:last-child {
            border-bottom: none;
        }

        .field-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 12px;
            background: #ffffff;
            border-bottom: 1px solid #d3dae6;
            cursor: pointer;
            font-weight: 400;
            font-size: 13px;
            color: #343741;
            position: relative;
            min-height: 32px;
        }

        .field-header:hover {
            background: #f7f9fb;
        }

        .field-header-left {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .field-collapse-arrow {
            width: 12px;
            height: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.15s ease;
            flex-shrink: 0;
        }

        .field-collapse-arrow.collapsed {
            transform: rotate(-90deg);
        }

        .field-collapse-arrow::before {
            content: '';
            width: 0;
            height: 0;
            border-left: 3px solid transparent;
            border-right: 3px solid transparent;
            border-top: 3px solid #69707d;
        }

        .field-count {
            background: #d3dae6;
            color: #69707d;
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 400;
            min-width: 18px;
            text-align: center;
            line-height: 1.3;
        }

        .field-list {
            max-height: 280px;
            overflow-y: auto;
        }

        .field-item {
            display: flex;
            align-items: center;
            padding: 4px 12px 4px 30px;
            cursor: pointer;
            font-size: 13px;
            border-radius: 0;
            transition: background-color 0.1s ease;
            min-height: 24px;
            color: #343741;
        }

        .field-item:hover {
            background: #f7f9fb;
        }

        .field-item.selected {
            background: #e7f3ff;
            border-left: 3px solid #006bb4;
        }

        .field-icon {
            width: 14px;
            height: 14px;
            margin-right: 6px;
            flex-shrink: 0;
        }

        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .query-bar {
            background: #ffffff;
            border-bottom: 1px solid #d3dae6;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .kql-input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #d3dae6;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            background: #ffffff;
            transition: border-color 0.2s ease;
        }

        .kql-input:focus {
            outline: none;
            border-color: #006bb4;
            box-shadow: 0 0 0 2px rgba(0, 107, 180, 0.1);
        }

        .toolbar {
            background: #ffffff;
            border-bottom: 1px solid #d3dae6;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .document-count {
            font-weight: 600;
            color: #343741;
        }

        .field-statistics {
            color: #69707d;
            font-size: 14px;
        }

        .toolbar-btn {
            background: none;
            border: 1px solid transparent;
            color: #343741;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            width: 32px;
            height: 32px;
            transition: all 0.2s ease;
        }

        .toolbar-btn:hover {
            background: #f7f9fb;
            border-color: #d3dae6;
        }

        .toolbar-btn.active {
            background: #e7f3ff;
            border-color: #006bb4;
            color: #006bb4;
        }



        .documents-container {
            flex: 1;
            background: #ffffff;
            overflow-y: auto;
        }

        .document-table {
            width: 100%;
            border-collapse: collapse;
        }

        .document-table th {
            background: #f7f9fb;
            border-bottom: 1px solid #d3dae6;
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            position: sticky;
            top: 0;
            color: #343741;
        }

        .document-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f2f5;
            vertical-align: top;
            font-size: 14px;
        }

        .document-table tr:hover {
            background: #f7f9fb;
        }

        .timestamp-cell {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 13px;
            white-space: nowrap;
            width: 180px;
            color: #69707d;
        }

        .summary-cell {
            max-width: 100%;
            word-break: break-word;
            line-height: 1.4;
        }

        .field-value {
            color: #006bb4;
            font-weight: 500;
        }

        .field-name {
            color: #69707d;
        }

        .expand-btn {
            background: none;
            border: none;
            cursor: pointer;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 3px;
        }

        .expand-btn:hover {
            background: #f0f2f5;
        }

        .loading-spinner {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: #69707d;
        }

        .error-message {
            background: #fef7f7;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px 16px;
            margin: 16px;
            border-radius: 4px;
            font-size: 13px;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            color: #69707d;
            text-align: center;
        }

        .hidden {
            display: none;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f3f4;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c7d0;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a5adba;
        }

        /* Additional styles for better UX */
        .expanded-row {
            background: #f7f9fb !important;
        }

        .expanded-row:hover {
            background: #f7f9fb !important;
        }

        .field-item:active {
            background: #e7f3ff;
        }

        .toolbar-btn:active {
            transform: scale(0.95);
        }

        /* Input focus styles */
        .search-input:focus {
            outline: none;
            border-color: #006bb4;
            box-shadow: 0 0 0 2px rgba(0, 107, 180, 0.1);
        }

        /* Loading state for buttons */
        .refresh-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Field section transitions */
        .field-list {
            transition: max-height 0.3s ease;
        }

        /* Better hover states */
        .document-table tr:hover .expand-btn {
            background: #e7f3ff;
        }


    </style>
</head>
<body>
    <!-- Kibana Header -->
    <div class="kibana-header">
        <div class="header-left">
            <div class="kibana-logo">
                <span class="icon icon-discover"></span>
                Discover
            </div>
            <div class="data-view-selector">
                <div class="data-view-dropdown" id="dataViewDropdown">
                    <span id="selectedDataView">Select data view...</span>
                </div>
                <div class="data-view-options" id="dataViewOptions">
                    <div class="data-view-header">Select data view</div>
                    <!-- Options will be populated here -->
                </div>
            </div>
        </div>
        <div class="header-right">
            <a href="{{ route('elasticsearch.create-alert') }}" class="action-btn">
                <span class="icon icon-add"></span>
                Create Alert
            </a>
            <a href="{{ route('elasticsearch.rules') }}" class="action-btn"> 
                <span class="icon icon-rules"></span>
                Rules
            </a>
            <a href="{{ route('elasticsearch.integrations') }}" class="action-btn">
                <span class="icon icon-integrations"></span>
                Integrations
            </a>
            <div class="time-picker">
                <span class="icon icon-calendar"></span>
                <span>Last 15 minutes</span>
            </div>
            <button class="refresh-btn" id="refreshBtn">
                <span class="icon icon-refresh"></span>
                Refresh
            </button>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Search Bar -->
            <div class="search-bar">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search field names" id="fieldSearch">
                    <span class="icon icon-search search-icon"></span>
                </div>
            </div>

            <!-- Field Sections Container -->
            <div class="field-sections-container">
            <!-- Popular Fields -->
            <div class="field-section">
                <div class="field-header" onclick="toggleFieldSection('popular')">
                    <div class="field-header-left">
                        <div class="field-collapse-arrow" id="popularArrow"></div>
                        <span>Popular fields</span>
                    </div>
                    <span class="field-count" id="popularCount">0</span>
                </div>
                <div class="field-list" id="popularFields">
                    <!-- Popular fields will be populated here -->
                </div>
            </div>

            <!-- Available Fields -->
            <div class="field-section">
                <div class="field-header" onclick="toggleFieldSection('available')">
                    <div class="field-header-left">
                        <div class="field-collapse-arrow" id="availableArrow"></div>
                        <span>Available fields</span>
                    </div>
                    <span class="field-count" id="availableCount">0</span>
                </div>
                <div class="field-list" id="availableFields">
                    <!-- Available fields will be populated here -->
                </div>
            </div>

            <!-- Empty Fields -->
            <div class="field-section">
                <div class="field-header" onclick="toggleFieldSection('empty')">
                    <div class="field-header-left">
                        <div class="field-collapse-arrow collapsed" id="emptyArrow"></div>
                        <span>Empty fields</span>
                    </div>
                    <span class="field-count" id="emptyCount">5</span>
                </div>
                <div class="field-list hidden" id="emptyFields">
                    <!-- Empty fields would be populated here -->
                </div>
            </div>

            <!-- Meta Fields -->
            <div class="field-section">
                <div class="field-header" onclick="toggleFieldSection('meta')">
                    <div class="field-header-left">
                        <div class="field-collapse-arrow" id="metaArrow"></div>
                        <span>Meta fields</span>
                    </div>
                    <span class="field-count" id="metaCount">4</span>
                </div>
                <div class="field-list" id="metaFields">
                    <div class="field-item">
                        <div class="field-icon icon-field-text"></div>
                        <span>_id</span>
                    </div>
                    <div class="field-item">
                        <div class="field-icon icon-field-text"></div>
                        <span>_ignored</span>
                    </div>
                    <div class="field-item">
                        <div class="field-icon icon-field-text"></div>
                        <span>_index</span>
                    </div>
                    <div class="field-item">
                        <div class="field-icon icon-field-number"></div>
                        <span>_score</span>
                    </div>
                </div>
            </div>
            </div>


        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Query Bar -->
            <div class="query-bar">
                <input type="text" class="kql-input" placeholder="Search documents..." id="searchInput">
            </div>

            <!-- Toolbar -->
            <div class="toolbar">
                <div class="toolbar-left">
                    <div class="document-count" id="documentCount">Documents (0)</div>
                    <span class="field-statistics">Field statistics</span>
                </div>
                <div class="toolbar-right">
                    <button class="toolbar-btn" id="sortBtn" onclick="toggleSortFields()" title="Sort fields">
                        <span class="icon icon-sort"></span>
                    </button>
                    <button class="toolbar-btn active" id="tableBtn" title="Table view">
                        <span class="icon icon-table"></span>
                    </button>
                </div>
            </div>



            <!-- Documents Container -->
            <div class="documents-container">
                <div id="loadingSpinner" class="loading-spinner hidden">
                    <div>⟳ Loading documents...</div>
                </div>
                
                <div id="errorContainer" class="hidden"></div>
                
                <div id="emptyState" class="empty-state hidden">
                    <div style="margin-bottom: 16px;">
                        <span class="icon icon-search" style="width: 48px; height: 48px; opacity: 0.5;"></span>
                    </div>
                    <h3>No documents found</h3>
                    <p>Select an index to start exploring your data</p>
                </div>

                <table class="document-table hidden" id="documentTable">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th style="width: 180px;">@timestamp</th>
                            <th>Summary</th>
                        </tr>
                    </thead>
                    <tbody id="documentTableBody">
                        <!-- Documents will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '/api/elasticsearch';
        let indexes = [];
        let selectedIndexData = null;
        let documents = [];
        let fields = [];
        let allFields = [];
        let sortAscending = true;

        // DOM elements
        const dataViewDropdown = document.getElementById('dataViewDropdown');
        const selectedDataView = document.getElementById('selectedDataView');
        const dataViewOptions = document.getElementById('dataViewOptions');
        const refreshBtn = document.getElementById('refreshBtn');
        const fieldSearch = document.getElementById('fieldSearch');
        const searchInput = document.getElementById('searchInput');
        const popularFields = document.getElementById('popularFields');
        const availableFields = document.getElementById('availableFields');
        const documentCount = document.getElementById('documentCount');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const errorContainer = document.getElementById('errorContainer');
        const emptyState = document.getElementById('emptyState');
        const documentTable = document.getElementById('documentTable');
        const documentTableBody = document.getElementById('documentTableBody');

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadIndexes();
            showEmptyState();
            
            // Dropdown functionality
            dataViewDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleDropdown();
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                closeDropdown();
            });

            refreshBtn.addEventListener('click', function() {
                // Clear all filters and searches
                searchInput.value = '';
                fieldSearch.value = '';
                
                if (selectedIndexData) {
                    loadIndexData(selectedIndexData.name);
                } else {
                    loadIndexes();
                }
            });

            fieldSearch.addEventListener('input', function() {
                filterFields(this.value);
            });

            searchInput.addEventListener('input', function() {
                applyDocumentFilter();
            });
        });

        async function loadIndexes() {
            try {
                selectedDataView.textContent = 'Loading...';
                dataViewDropdown.style.pointerEvents = 'none';
                
                const response = await fetch(`${API_BASE}/indexes`);
                const data = await response.json();
                
                if (response.ok) {
                    indexes = data;
                    populateIndexDropdown(data);
                } else {
                    showError('Failed to load indexes: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                showError('Connection error: Unable to connect to Elasticsearch');
            } finally {
                dataViewDropdown.style.pointerEvents = 'auto';
            }
        }

        function populateIndexDropdown(indexes) {
            // Clear existing options except header
            const header = dataViewOptions.querySelector('.data-view-header');
            dataViewOptions.innerHTML = '';
            dataViewOptions.appendChild(header);
            
            // Reset selected text
            selectedDataView.textContent = 'Select data view...';
            
            indexes.forEach(index => {
                const option = document.createElement('div');
                option.className = 'data-view-option';
                option.textContent = index.display_name;
                option.setAttribute('data-index', index.name);
                
                option.addEventListener('click', function(e) {
                    e.stopPropagation();
                    selectDataView(index);
                });
                
                dataViewOptions.appendChild(option);
            });
        }

        function selectDataView(indexData) {
            selectedIndexData = indexData;
            selectedDataView.textContent = indexData.display_name;
            
            // Update selected state
            document.querySelectorAll('.data-view-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            document.querySelector(`[data-index="${indexData.name}"]`).classList.add('selected');
            
            closeDropdown();
            loadIndexData(indexData.name);
        }

        function toggleDropdown() {
            const isOpen = dataViewOptions.classList.contains('show');
            if (isOpen) {
                closeDropdown();
            } else {
                openDropdown();
            }
        }

        function openDropdown() {
            dataViewDropdown.classList.add('open');
            dataViewOptions.classList.add('show');
        }

        function closeDropdown() {
            dataViewDropdown.classList.remove('open');
            dataViewOptions.classList.remove('show');
        }

        async function loadIndexData(indexName) {
            showLoading();
            
            try {
                const response = await fetch(`${API_BASE}/data?index=${encodeURIComponent(indexName)}`);
                const data = await response.json();
                
                if (response.ok) {
                    documents = data.documents || [];
                    extractFields(documents);
                    displayDocuments(documents);
                    updateDocumentCount(data.total || 0);
                } else {
                    showError('Failed to load data: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                showError('Connection error: Unable to fetch index data');
            }
        }

        function extractFields(docs) {
            const fieldSet = new Set();
            const fieldValues = new Map(); // Track field values to determine popularity and emptiness
            
            docs.forEach(doc => {
                function addFields(obj, prefix = '') {
                    Object.keys(obj).forEach(key => {
                        const fullKey = prefix ? `${prefix}.${key}` : key;
                        fieldSet.add(fullKey);
                        
                        // Track field values for analysis
                        if (!fieldValues.has(fullKey)) {
                            fieldValues.set(fullKey, {
                                count: 0,
                                nonEmptyCount: 0,
                                values: new Set()
                            });
                        }
                        
                        const fieldInfo = fieldValues.get(fullKey);
                        fieldInfo.count++;
                        
                        const value = obj[key];
                        if (value !== null && value !== undefined && value !== '') {
                            fieldInfo.nonEmptyCount++;
                            fieldInfo.values.add(String(value).substring(0, 50)); // Sample values
                        }
                        
                        if (typeof obj[key] === 'object' && obj[key] !== null && !Array.isArray(obj[key])) {
                            addFields(obj[key], fullKey);
                        }
                    });
                }
                
                if (doc.source) {
                    addFields(doc.source);
                }
            });

            allFields = Array.from(fieldSet).sort();
            
            // Analyze fields for categorization
            const totalDocs = docs.length;
            const popularThreshold = Math.max(1, Math.floor(totalDocs * 0.8)); // Fields in 80%+ of docs
            const emptyThreshold = Math.max(1, Math.floor(totalDocs * 0.1)); // Fields in <10% of docs
            
            window.fieldAnalysis = {
                popular: [],
                available: [],
                empty: [],
                meta: ['_id', '_index', '_score', '_ignored']
            };
            
            allFields.forEach(field => {
                if (field.startsWith('_')) {
                    // Skip meta fields, they're handled separately
                    return;
                }
                
                const fieldInfo = fieldValues.get(field);
                if (fieldInfo) {
                    if (fieldInfo.nonEmptyCount >= popularThreshold) {
                        window.fieldAnalysis.popular.push(field);
                    } else if (fieldInfo.nonEmptyCount <= emptyThreshold) {
                        window.fieldAnalysis.empty.push(field);
                    } else {
                        window.fieldAnalysis.available.push(field);
                    }
                }
            });
            
            fields = [...allFields];
            updateFieldLists();
        }

        function updateFieldLists() {
            if (!window.fieldAnalysis) {
                // Initialize empty if no analysis yet
                window.fieldAnalysis = {
                    popular: [],
                    available: [],
                    empty: [],
                    meta: ['_id', '_index', '_score', '_ignored']
                };
            }

            // Update Popular fields
            popularFields.innerHTML = '';
            window.fieldAnalysis.popular.forEach(field => {
                const fieldItem = createFieldItem(field);
                popularFields.appendChild(fieldItem);
            });

            // Update Available fields
            availableFields.innerHTML = '';
            window.fieldAnalysis.available.forEach(field => {
                const fieldItem = createFieldItem(field);
                availableFields.appendChild(fieldItem);
            });

            // Update Empty fields
            const emptyFieldsList = document.getElementById('emptyFields');
            emptyFieldsList.innerHTML = '';
            window.fieldAnalysis.empty.forEach(field => {
                const fieldItem = createFieldItem(field);
                emptyFieldsList.appendChild(fieldItem);
            });

            // Update counts
            document.getElementById('popularCount').textContent = window.fieldAnalysis.popular.length;
            document.getElementById('availableCount').textContent = window.fieldAnalysis.available.length;
            document.getElementById('emptyCount').textContent = window.fieldAnalysis.empty.length;
        }

        function createFieldItem(fieldName) {
            const item = document.createElement('div');
            item.className = 'field-item';
            item.setAttribute('data-field', fieldName);
            
            const icon = document.createElement('div');
            icon.className = 'field-icon';
            
            // Determine field type based on name
            if (fieldName.includes('timestamp') || fieldName.includes('time')) {
                icon.className += ' icon-field-date';
            } else if (fieldName.includes('count') || fieldName.includes('size') || fieldName.includes('port') || fieldName.includes('number')) {
                icon.className += ' icon-field-number';
            } else if (fieldName.includes('geo') || fieldName.includes('location')) {
                icon.className += ' icon-field-geo';
            } else {
                icon.className += ' icon-field-text';
            }
            
            const name = document.createElement('span');
            name.textContent = fieldName;
            
            item.appendChild(icon);
            item.appendChild(name);
            
            // Add click handler
            item.addEventListener('click', function() {
                searchByField(fieldName);
            });
            
            return item;
        }

        function displayDocuments(docs) {
            if (docs.length === 0) {
                showEmptyState();
                return;
            }

            hideEmptyState();
            hideError();
            documentTable.classList.remove('hidden');

            documentTableBody.innerHTML = '';
            
            docs.forEach((doc, index) => {
                const row = document.createElement('tr');
                
                // Expand button
                const expandCell = document.createElement('td');
                const expandBtn = document.createElement('button');
                expandBtn.className = 'expand-btn';
                const expandIcon = document.createElement('span');
                expandIcon.className = 'icon icon-arrow-right';
                expandBtn.appendChild(expandIcon);
                expandBtn.onclick = () => toggleDocumentExpansion(index);
                expandCell.appendChild(expandBtn);
                row.appendChild(expandCell);
                
                // Timestamp
                const timeCell = document.createElement('td');
                timeCell.className = 'timestamp-cell';
                if (doc.timestamp) {
                    const date = new Date(doc.timestamp);
                    timeCell.textContent = date.toLocaleString('en-US', {
                        month: 'short',
                        day: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                } else {
                    timeCell.textContent = 'May 26, 2025 @ 13:47:31.852';
                }
                row.appendChild(timeCell);
                
                // Summary
                const summaryCell = document.createElement('td');
                summaryCell.className = 'summary-cell';
                summaryCell.innerHTML = createSummary(doc.source);
                row.appendChild(summaryCell);
                
                documentTableBody.appendChild(row);
            });
        }

        function createSummary(source) {
            const importantFields = ['@timestamp', 'agent.ephemeral_id', 'agent.hostname', 'agent.id', 'agent.name', 'agent.type', 'agent.version', 'ecs.version', 'host.name', 'input.type', 'log.file.path', 'log.offset', 'message'];
            
            let summary = '';
            importantFields.forEach(field => {
                const value = getNestedValue(source, field);
                if (value !== undefined && value !== null) {
                    summary += `<span class="field-name">${field}</span> <span class="field-value">${value}</span> `;
                }
            });
            
            return summary || JSON.stringify(source).substring(0, 200) + '...';
        }

        function getNestedValue(obj, path) {
            return path.split('.').reduce((current, key) => {
                return current && current[key] !== undefined ? current[key] : undefined;
            }, obj);
        }

        function toggleDocumentExpansion(index) {
            // This would expand/collapse document details
            console.log('Toggle expansion for document', index);
        }

        function updateDocumentCount(total) {
            documentCount.textContent = `Documents (${total.toLocaleString()})`;
        }

        function filterFields(searchTerm) {
            const searchLower = searchTerm.toLowerCase().trim();
            
            if (!window.fieldAnalysis) {
                return;
            }
            
            // Filter each category separately
            const filterField = (field) => !searchLower || field.toLowerCase().includes(searchLower);
            
            const filteredPopular = window.fieldAnalysis.popular.filter(filterField);
            const filteredAvailable = window.fieldAnalysis.available.filter(filterField);
            const filteredEmpty = window.fieldAnalysis.empty.filter(filterField);

            // Update popular fields
            popularFields.innerHTML = '';
            filteredPopular.forEach(field => {
                const fieldItem = createFieldItem(field);
                popularFields.appendChild(fieldItem);
            });

            // Update available fields
            availableFields.innerHTML = '';
            filteredAvailable.forEach(field => {
                const fieldItem = createFieldItem(field);
                availableFields.appendChild(fieldItem);
            });

            // Update empty fields
            const emptyFieldsList = document.getElementById('emptyFields');
            emptyFieldsList.innerHTML = '';
            filteredEmpty.forEach(field => {
                const fieldItem = createFieldItem(field);
                emptyFieldsList.appendChild(fieldItem);
            });

            // Update counts
            document.getElementById('popularCount').textContent = filteredPopular.length;
            document.getElementById('availableCount').textContent = filteredAvailable.length;
            document.getElementById('emptyCount').textContent = filteredEmpty.length;
        }

        function showLoading() {
            hideEmptyState();
            hideError();
            documentTable.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');
        }

        function showEmptyState() {
            hideError();
            documentTable.classList.add('hidden');
            loadingSpinner.classList.add('hidden');
            emptyState.classList.remove('hidden');
        }

        function hideEmptyState() {
            emptyState.classList.add('hidden');
            loadingSpinner.classList.add('hidden');
        }

        function showError(message) {
            hideEmptyState();
            documentTable.classList.add('hidden');
            loadingSpinner.classList.add('hidden');
            errorContainer.innerHTML = `<div class="error-message">${message}</div>`;
            errorContainer.classList.remove('hidden');
        }

        function hideError() {
            errorContainer.classList.add('hidden');
        }

        // Search functions
        function searchByField(fieldName) {
            // Find a document that has this field and get a sample value
            for (let doc of documents) {
                const sampleValue = getNestedValue(doc.source, fieldName);
                if (sampleValue !== undefined && sampleValue !== null && sampleValue !== '') {
                    searchInput.value = String(sampleValue);
                    applyDocumentFilter();
                    break;
                }
            }
        }

        function applyDocumentFilter() {
            const searchTerm = searchInput.value.trim().toLowerCase();
            
            // If no search term, show all documents
            if (!searchTerm) {
                displayDocuments(documents);
                updateDocumentCount(documents.length);
                return;
            }
            
            if (!selectedIndexData) return;
            
            // Simple text search across all document fields
            const filteredDocuments = documents.filter(doc => {
                return searchInObject(doc.source, searchTerm);
            });
            
            displayDocuments(filteredDocuments);
            updateDocumentCount(filteredDocuments.length);
        }

        function searchInObject(obj, searchTerm) {
            if (obj === null || obj === undefined) return false;
            
            if (typeof obj === 'string') {
                return obj.toLowerCase().includes(searchTerm);
            }
            
            if (typeof obj === 'number') {
                return String(obj).includes(searchTerm);
            }
            
            if (typeof obj === 'object') {
                if (Array.isArray(obj)) {
                    return obj.some(item => searchInObject(item, searchTerm));
                } else {
                    return Object.values(obj).some(value => searchInObject(value, searchTerm));
                }
            }
            
            return false;
        }

        // UI Functions
        function toggleFieldSection(sectionName) {
            const fieldList = document.getElementById(sectionName + 'Fields');
            const arrow = document.getElementById(sectionName + 'Arrow');
            
            if (fieldList) {
                fieldList.classList.toggle('hidden');
                
                // Toggle arrow direction
                if (arrow) {
                    arrow.classList.toggle('collapsed');
                }
            }
        }

        function toggleSortFields() {
            const sortBtn = document.getElementById('sortBtn');
            sortAscending = !sortAscending;
            
            // Sort all fields
            allFields.sort((a, b) => {
                return sortAscending ? a.localeCompare(b) : b.localeCompare(a);
            });
            
            // Re-filter to maintain search
            const searchTerm = fieldSearch.value;
            filterFields(searchTerm);
            
            // Visual feedback
            sortBtn.classList.toggle('active');
            console.log('Fields sorted:', sortAscending ? 'A-Z' : 'Z-A');
        }

        function toggleDocumentExpansion(index) {
            // Toggle document expansion
            const row = documentTableBody.children[index];
            if (row) {
                const expandBtn = row.querySelector('.expand-btn .icon');
                if (expandBtn.classList.contains('icon-arrow-right')) {
                    expandBtn.classList.remove('icon-arrow-right');
                    expandBtn.classList.add('icon-arrow-down');
                    
                    // Create expanded row
                    const expandedRow = document.createElement('tr');
                    expandedRow.className = 'expanded-row';
                    expandedRow.innerHTML = `
                        <td colspan="3" style="padding: 16px; background: #f7f9fb; border-left: 3px solid #006bb4;">
                            <pre style="font-family: Monaco, monospace; font-size: 12px; margin: 0; white-space: pre-wrap;">${JSON.stringify(documents[index].source, null, 2)}</pre>
                        </td>
                    `;
                    
                    // Insert after current row
                    row.parentNode.insertBefore(expandedRow, row.nextSibling);
                } else {
                    expandBtn.classList.remove('icon-arrow-down');
                    expandBtn.classList.add('icon-arrow-right');
                    
                    // Remove expanded row
                    const nextRow = row.nextElementSibling;
                    if (nextRow && nextRow.classList.contains('expanded-row')) {
                        nextRow.remove();
                    }
                }
            }
        }


        function openCreateAlert() {
            window.open('/elasticsearch/create-alert', '_blank');
        }

    </script>
</body>
</html> 