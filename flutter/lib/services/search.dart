import 'package:resold/enums/sort.dart';
import 'package:resold/helpers/category-helper.dart';
import 'package:resold/helpers/condition-helper.dart';
import 'package:resold/models/product.dart';
import 'package:elastic_client/elastic_client.dart' as elastic;
import 'package:elastic_client/console_http_transport.dart';
import 'package:resold/state/screens/search-state.dart';

/*
* Resold search service - Resold search specific API client
* This service is used to make elastic-search requests
*/
class Search {
  static final searchUrl = '<your elastic search url>';
  static final searchIndex = 'magento*';
  static final searchType = 'geo_point';

  static final transport = ConsoleHttpTransport(Uri.parse(searchUrl));
  static final client = elastic.Client(transport);
  static final itemsPerPage = 20;

  /*
   * fetchSearchProducts - Returns products given a location and search term
   * state - Search state
   * latitude - latitude of local location
   * longitude - longitude of local location
   * offset - (optional) page offset
   */
  static Future<List<Product>> fetchSearchProducts(SearchState state, double latitude, double longitude,
      {int offset = 0}) async {
    Map<dynamic, dynamic> query;
    List<Map<dynamic, dynamic>> sort = [];
    String searchTerm = state.textController.text;

    // add distance filter
    List<dynamic> filters = [
      elastic.Query.bool(filter: {
        'geo_distance': {
          'distance': '${state.distance}mi',
          'location_raw': {'lat': latitude, 'lon': longitude}
        }
      })
    ];

    // add search term filter
    if (searchTerm.isNotEmpty) {
      filters.add(elastic.Query.bool(should: [
        elastic.Query.match('name', searchTerm),
        elastic.Query.match('description_raw', searchTerm),
        elastic.Query.match('title_description_raw', searchTerm),
        elastic.Query.match('description', searchTerm),
        elastic.Query.match('title_description', searchTerm),
      ]));
    } // end if search term is not empty

    // add category filter
    if (state.selectedCategory != 'Cancel') {
      String categoryId = CategoryHelper.getCategoryIdByName(state.selectedCategory);
      filters.add(elastic.Query.bool(should: [
        elastic.Query.match('category_ids_raw', categoryId),
      ]));
    } // end if selected category

    // add condition filter
    if (state.selectedCondition != 'Cancel') {
      String conditionId = ConditionHelper.getConditionIdByName(state.selectedCondition);
      filters.add(elastic.Query.bool(should: [
        elastic.Query.match('condition_raw', conditionId),
      ]));
    } // end if selected condition

    // add sort
    switch (state.selectedSort) {
      case Sort.newest:
        sort.add({'id': 'desc'});
        break;
      case Sort.distance:
        sort.add({
          '_geo_distance': {'location_raw': '$latitude,$longitude', 'order': 'asc', 'unit': 'km'}
        });
        break;
      case Sort.lowToHigh:
        sort.add({'price_raw': 'asc'});
        break;
      case Sort.highToLow:
        sort.add({'price_raw': 'desc'});
    } // end switch case adding sort

    // setup elastic search query with filters
    query = elastic.Query.bool(must: [elastic.Query.matchAll()], filter: filters);

    // submit query with elastic search client
    final searchResults = await client.search(searchIndex, searchType, query,
        source: true, offset: offset, limit: itemsPerPage, sort: sort);

    // add products to result list
    List<Product> products = new List<Product>();
    searchResults.hits.forEach((doc) {
      if (doc.doc['name'] != null) {
        return products.add(Product.fromDoc(doc.doc));
      } // end if we have a valid document
    });

    return products;
  } // end function fetchSearchProducts

} // end class Search
