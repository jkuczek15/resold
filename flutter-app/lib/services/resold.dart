import 'package:resold/models/product.dart';
import 'package:elastic_client/elastic_client.dart' as elastic;
import 'package:elastic_client/console_http_transport.dart';

class Api {

  static final searchUrl = 'https://search-resold-es-iy75wommvnfqf5hf7p6w7ej2sq.us-west-2.es.amazonaws.com';
  static final searchIndex = 'magento*';
  static final searchType = 'geo_point';

  static final transport = ConsoleHttpTransport(Uri.parse(searchUrl));
  static final client = elastic.Client(transport);
  static final itemsPerPage = 20;

  static Future<List<Product>> fetchLocalProducts(double latitude, double longitude, {int offset = 0}) async {

    var query = elastic.Query.bool(
      must: elastic.Query.matchAll(),
      filter: {
        'geo_distance': {
          'distance': '50mi',
          'location_raw': {
            'lat': latitude,
            'lon' : longitude
          }
        }
      }
    );

    var sort = [{
      '_geo_distance' : {
        'location_raw': {
          'lat': latitude,
          'lon' : longitude
        },
        'order' : 'asc',
        'unit' : 'mi'
      }
    }];

    final searchResults = await client.search(searchIndex, searchType, query, source: true, offset: offset, limit: itemsPerPage, sort: sort);

    List<Product> products = new List<Product>();
    searchResults.hits.forEach((doc) => products.add(Product.fromDoc(doc.doc)));

    return products;
  }

  static Future<List<Product>> fetchSearchProducts(term, {int offset = 0}) async {

    var query = elastic.Query.bool(should: [
      elastic.Query.match('name', term),
      elastic.Query.match('description_raw', term),
      elastic.Query.match('title_description_raw', term),
      elastic.Query.match('description', term),
      elastic.Query.match('title_description', term)
    ]);

    final searchResults = await client.search(searchIndex, searchType, query, source: true, offset: offset, limit: itemsPerPage);

    List<Product> products = new List<Product>();
    searchResults.hits.forEach((doc) => products.add(Product.fromDoc(doc.doc)));

    return products;
  }
}