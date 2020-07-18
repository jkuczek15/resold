import 'package:http/http.dart' as http;
import '../models/product.dart';
import 'dart:convert';
import 'package:elastic_client/elastic_client.dart' as elastic;
import 'package:elastic_client/console_http_transport.dart';

class Api {

  static final searchUrl = 'https://search-resold-es-iy75wommvnfqf5hf7p6w7ej2sq.us-west-2.es.amazonaws.com';
  static final searchIndex = 'magento*';
  static final searchType = 'doc';

  static Future<List<Product>> fetchProducts() async {

    final transport = ConsoleHttpTransport(Uri.parse(searchUrl));
    final client = elastic.Client(transport);

    final searchResults = await client.search(searchIndex, searchType, null, source: true);

    List<Product> products = new List<Product>();
    searchResults.hits.forEach((doc) => products.add(Product.fromDoc(doc.doc)));

    return products;
  }
}