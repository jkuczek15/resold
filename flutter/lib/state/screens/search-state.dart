import 'dart:async';

import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/enums/sort.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/search.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class SearchState {
  TextEditingController textController;
  StreamController<List<Product>> searchStream;
  List<Product> initialProducts;
  String selectedCategory;
  String selectedCondition;
  Sort selectedSort;
  String distance;
  int currentPage;

  SearchState(
      {this.textController,
      this.searchStream,
      this.selectedCategory,
      this.selectedCondition,
      this.selectedSort,
      this.distance,
      this.currentPage});

  static Future<SearchState> initialState(CustomerResponse customer, Position currentLocation) async {
    SearchState searchState = SearchState(
        currentPage: 0,
        distance: '25',
        selectedCategory: 'Cancel',
        selectedCondition: 'Cancel',
        selectedSort: Sort.newest,
        textController: TextEditingController(),
        searchStream: StreamController<List<Product>>.broadcast());
    if (customer.isLoggedIn()) {
      searchState.initialProducts =
          await Search.fetchSearchProducts(searchState, currentLocation.latitude, currentLocation.longitude);
    } // end if customer is logged in
    return searchState;
  } // end function initialState
}
