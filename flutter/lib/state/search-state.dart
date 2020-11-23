import 'package:flutter/material.dart';
import 'package:resold/enums/sort.dart';

class SearchState {
  TextEditingController searchBarController;
  String selectedCategory;
  String selectedCondition;
  Sort selectedSort;
  String distance;

  SearchState(
      {this.searchBarController, this.selectedCategory, this.selectedCondition, this.selectedSort, this.distance});
}
