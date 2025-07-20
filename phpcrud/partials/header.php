<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Task Manager</title>
    <link href="../styles/main.css" rel="stylesheet" />
    <!-- Add this in your header or layout -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background-color: #f3f4f6;
            /* bg-gray-100 */
            color: #111827;
            /* text-gray-900 */
            padding: 1.5rem;
            /* p-6 */
            margin: 1rem;
            /* m-4 */
            border: 1px solid #e5e7eb;
            /* border */
            border-radius: 0.5rem;
            /* rounded-lg */
            box-shadow: 0 0px 5px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            /* shadow-lg */
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .container {
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            /* px-4 */
            padding-right: 1rem;
            padding-top: 1.5rem;
            /* py-6 */
            padding-bottom: 1.5rem;
        }

        h1 {
            font-size: 1.875rem;
            /* text-3xl */
            font-weight: 600;
            /* font-semibold */
            margin-bottom: 1.5rem;
            /* mb-6 */
        }

        .footer {
            margin-bottom: 4rem;
            padding: 1rem 0;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            /* Tailwind gray-500 */
            font-size: 0.875rem;
            /* text-sm */
            user-select: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>📋 Task Manager PHP</h1>