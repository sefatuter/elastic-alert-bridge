<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Elastic-Alert‐Bridge Dashboard</title>
  <style>
    /* Genel body stilleri */
    body {
      margin: 0;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background-color: #f5f7fa;
      color: #333;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    /* Başlık stili */
    h1 {
      font-size: 2.5rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: #1a73e8;
      text-shadow: 1px 1px rgba(0, 0, 0, 0.1);
    }

    /* Linki buton formuna sokan kapsayıcı */
    .button-container {
      margin-top: 1rem;
    }

    /* “Browse Elasticsearch Indexes” buton stili */
    .btn-elastic {
      display: inline-block;
      text-decoration: none;
      background-color: #1a73e8;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: 500;
      transition: background-color 0.2s ease, transform 0.1s ease;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .btn-elastic:hover {
      background-color: #155bbf;
      transform: translateY(-1px);
    }

    .btn-elastic:active {
      background-color: #134fa2;
      transform: translateY(0);
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
    }

    /* Küçük açıklama metni stili (isteğe bağlı) */
    .subtitle {
      font-size: 1rem;
      color: #555;
      margin-top: 0.5rem;
      max-width: 600px;
      text-align: center;
      line-height: 1.4;
    }
  </style>
</head>
<body>
  <h1>Welcome to the Elastic-Alert-Bridge Dashboard</h1>
  <div class="subtitle">
    Centralized monitoring and alert management for your Elasticsearch data. Navigate below to explore your indexes.
  </div>
  <div class="button-container">
    <a href="/elasticsearch" class="btn-elastic">🔍 Browse Elasticsearch Indexes</a>
  </div>
</body>
</html>
