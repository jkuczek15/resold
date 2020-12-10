import 'dart:async';

import 'package:flutter/material.dart';
import 'package:resold/enums/sort.dart';
import 'package:resold/models/product.dart';

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

  factory SearchState.initialState() {
    return SearchState(
        currentPage: 0,
        distance: '25',
        selectedCategory: 'Cancel',
        selectedCondition: 'Cancel',
        selectedSort: Sort.newest,
        textController: TextEditingController(),
        searchStream: StreamController<List<Product>>.broadcast());
  } // end function initialState
}
