import 'package:http/http.dart' as http;
import './models.dart' as models;
import 'dart:convert';
import 'package:elastic_client/elastic_client.dart' as elastic;
import 'package:elastic_client/console_http_transport.dart';

class Api {

  static final searchUrl = 'https://search-resold-es-iy75wommvnfqf5hf7p6w7ej2sq.us-west-2.es.amazonaws.com';
  static final searchIndex = 'magento*';
  static final searchType = 'doc';

  static Future<models.Album> fetchProducts() async {

    final transport = ConsoleHttpTransport(Uri.parse(searchUrl));
    final client = elastic.Client(transport);

    final ress = await client.indexExists(searchIndex);

    final rs1 = await client.search(searchIndex, searchType, null, source: true);

    print(rs1.toMap());

    final response = await http.post(searchUrl);

    if (response.statusCode == 200) {
      // If the server did return a 200 OK response,
      // then parse the JSON.
      return models.Album.fromJson(json.decode(response.body));
    } else {
      // If the server did not return a 200 OK response,
      // then throw an exception.
      throw Exception('Failed to load album');
    }
  }

  static Future<models.Album> fetchAlbum() async {
    final response = await http.get('https://jsonplaceholder.typicode.com/albums/1');

    if (response.statusCode == 200) {
      // If the server did return a 200 OK response,
      // then parse the JSON.
      return models.Album.fromJson(json.decode(response.body));
    } else {
      // If the server did not return a 200 OK response,
      // then throw an exception.
      throw Exception('Failed to load album');
    }
  }

}