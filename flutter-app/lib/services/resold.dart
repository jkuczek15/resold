import '../models/product.dart';
import 'package:elastic_client/elastic_client.dart' as elastic;
import 'package:elastic_client/console_http_transport.dart';

class Api {

  static final searchUrl = 'https://search-resold-es-iy75wommvnfqf5hf7p6w7ej2sq.us-west-2.es.amazonaws.com';
  static final searchIndex = 'magento*';
  static final searchType = 'doc';

  static Future<List<Product>> fetchLocalProducts() async {

    final transport = ConsoleHttpTransport(Uri.parse(searchUrl));
    final client = elastic.Client(transport);

    final searchResults = await client.search(searchIndex, searchType, null, source: true);

    List<Product> products = new List<Product>();
    searchResults.hits.forEach((doc) => products.add(Product.fromDoc(doc.doc)));

    return products;
  }

  static Future<List<Product>> fetchSearchProducts(term) async {

    final transport = ConsoleHttpTransport(Uri.parse(searchUrl));
    final client = elastic.Client(transport);

    final searchResults = await client.search(searchIndex, searchType, elastic.Query.term('name', term), source: true);

    List<Product> products = new List<Product>();
    searchResults.hits.forEach((doc) => products.add(Product.fromDoc(doc.doc)));

    return products;
  }
}