import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/enums/sort.dart';
import 'package:resold/state/actions/filter-search-results.dart';
import 'package:resold/state/screens/search-state.dart';
import 'package:resold/ui-models/product-ui-model.dart';

class ScrollableFilterList extends StatefulWidget {
  final SearchState searchState;
  final Position currentLocation;
  final Function dispatcher;

  ScrollableFilterList(
      {SearchState searchState, Position currentLocation, ProductUiModel model, Function dispatcher, Key key})
      : searchState = searchState,
        currentLocation = currentLocation,
        dispatcher = dispatcher,
        super(key: key);

  @override
  ScrollableFilterListState createState() => new ScrollableFilterListState(currentLocation, searchState, dispatcher);
}

class ScrollableFilterListState extends State<ScrollableFilterList> {
  //List categories =
  Map categoriesMap = {
    'Electronics': Icons.computer,
    'Fashion': MdiIcons.tshirtCrew,
    'Home & Lawn': MdiIcons.sofa,
    'Outdoors': Icons.directions_bike,
    'Music': MdiIcons.guitarAcoustic,
    'Collectibles': MdiIcons.cards,
    'Handmade': MdiIcons.handHeart,
    'Cancel': MdiIcons.close,
  };
  Map conditionMap = {
    'New': MdiIcons.emoticonExcitedOutline,
    'Like New': MdiIcons.emoticonHappyOutline,
    'Good': MdiIcons.emoticonNeutralOutline,
    'Used': MdiIcons.emoticonSadOutline,
    'Cancel': MdiIcons.close,
  };
  List basesortlist = ['Newest', 'Nearby', '(\$) Low to High', '(\$) High to Low'];
  double _currentSliderValue;
  int miles;
  List<bool> checkedSortList;

  TextStyle categoryTextStyle = new TextStyle();

  Column dropdownCategory;
  Column dropdownCondition;

  final SearchState searchState;
  final Position currentLocation;
  final Function dispatcher;
  ScrollableFilterListState(this.currentLocation, this.searchState, this.dispatcher);

  @override
  void initState() {
    super.initState();
    setState(() {
      miles = int.tryParse(searchState.distance);
      _currentSliderValue = miles.toDouble();

      onSelectedCategory(PopupMenuItem(value: searchState.selectedCategory, child: Text(searchState.selectedCategory)));

      onSelectedCondition(
          PopupMenuItem(value: searchState.selectedCondition, child: Text(searchState.selectedCondition)));

      onSelectedSort(
          PopupMenuItem(value: searchState.selectedSort.index, child: Text(searchState.selectedSort.index.toString())));
    });
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
        padding: EdgeInsets.fromLTRB(0, 0, 0, 10),
        child: Row(mainAxisAlignment: MainAxisAlignment.spaceEvenly, children: <Widget>[
          PopupMenuButton<PopupMenuItem>(
              child: dropdownCategory,
              onSelected: (PopupMenuItem item) => onSelectedCategory(item, dispatcher: dispatcher),
              itemBuilder: (BuildContext context) {
                var categorylist = List<PopupMenuItem>();
                categoriesMap.forEach((t, i) => categorylist.add(
                      PopupMenuItem(
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          children: [
                            Icon(i),
                            SizedBox(width: 10),
                            Text(t),
                          ],
                        ),
                        value: PopupMenuItem(value: t, child: Text(t)),
                      ),
                    ));
                return categorylist.map((PopupMenuItem choice) {
                  return PopupMenuItem<PopupMenuItem>(
                    value: choice,
                    child: choice,
                  );
                }).toList();
              }),
          PopupMenuButton<PopupMenuItem>(
              child: Column(
                children: [
                  Icon(MdiIcons.mapMarkerRadius),
                  Text('Distance'),
                ],
              ),
              itemBuilder: (BuildContext context) {
                return <PopupMenuEntry<PopupMenuItem<Slider>>>[
                  PopupMenuItem(
                    enabled: false,
                    child: StatefulBuilder(builder: (BuildContext context, StateSetter setState) {
                      return Column(children: [
                        Text('$miles mi'),
                        Slider(
                          value: _currentSliderValue,
                          min: 5,
                          max: 50,
                          divisions: 9,
                          activeColor: ResoldBlue,
                          inactiveColor: Colors.grey[300],
                          label: _currentSliderValue.round().toString(),
                          onChanged: (double value) {
                            setState(() {
                              _currentSliderValue = value;
                              miles = _currentSliderValue.toInt();
                            });
                          },
                          onChangeEnd: (double value) {
                            searchState.distance = _currentSliderValue.toInt().toString();
                            dispatcher(FilterSearchResultsAction(searchState));
                          },
                        ),
                      ]);
                    }),
                  )
                ];
              }),
          PopupMenuButton<PopupMenuItem>(
              child: dropdownCondition,
              onSelected: (PopupMenuItem item) => onSelectedCondition(item, dispatcher: dispatcher),
              itemBuilder: (BuildContext context) {
                var conditionList = List<PopupMenuItem>();
                conditionMap.forEach((t, i) => conditionList.add(
                      PopupMenuItem(
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          children: [
                            Icon(i),
                            SizedBox(width: 10),
                            Text(t),
                          ],
                        ),
                        value: PopupMenuItem(value: t, child: Text(t)),
                      ),
                    ));
                return conditionList.map((PopupMenuItem choice) {
                  return PopupMenuItem<PopupMenuItem>(
                    value: choice,
                    child: choice,
                  );
                }).toList();
              }),
          PopupMenuButton<PopupMenuItem>(
              child: Column(
                children: [
                  Icon(MdiIcons.sort),
                  Text('Sort'),
                ],
              ),
              onSelected: (PopupMenuItem item) => onSelectedSort(item, dispatcher: dispatcher),
              itemBuilder: (BuildContext context) {
                var sortlist = List.generate(
                  basesortlist.length,
                  (index) => PopupMenuItem<PopupMenuItem>(
                    value: PopupMenuItem(value: index, child: Text(index.toString())),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        Text(basesortlist[index]),
                        SizedBox(
                          width: 10,
                        ),
                        Icon(
                          MdiIcons.check,
                          color: checkedSortList[index] ? ResoldBlue : Colors.white,
                        ),
                      ],
                    ),
                  ),
                );
                return sortlist.map((PopupMenuItem choice) {
                  return PopupMenuItem<PopupMenuItem>(
                    value: choice,
                    child: choice,
                  );
                }).toList();
              }),
        ]));
  } // end function build

  // ignore: non_constant_identifier_names
  void onSelectedCategory(PopupMenuItem choice, {Function dispatcher}) async {
    setState(() {
      dropdownCategory = Column(
        children: [
          choice.value == 'Cancel' ? Icon(MdiIcons.filterMenu) : Icon(categoriesMap[choice.value]),
          choice.value == 'Cancel' ? Text('Category') : Text(choice.value)
        ],
      );
    });
    if (dispatcher is Function) {
      searchState.selectedCategory = choice.value;
      dispatcher(FilterSearchResultsAction(searchState));
    } // end if dispatcher is function
  } // end function onSelectedCategory

  void onSelectedCondition(PopupMenuItem choice, {Function dispatcher}) async {
    setState(() {
      dropdownCondition = Column(
        children: [
          choice.value == 'Cancel' ? Icon(MdiIcons.sparkles) : Icon(conditionMap[choice.value]),
          choice.value == 'Cancel' ? Text('Condition') : Text(choice.value)
        ],
      );
    });
    if (dispatcher is Function) {
      searchState.selectedCondition = choice.value;
      dispatcher(FilterSearchResultsAction(searchState));
    } // end if dispatcher is function
  } // end function onSelectedCondition

  void onSelectedSort(PopupMenuItem choice, {Function dispatcher}) async {
    setState(() {
      checkedSortList = [false, false, false, false];
      checkedSortList[choice.value] = true;
    });
    if (dispatcher is Function) {
      searchState.selectedSort = Sort.values[choice.value];
      dispatcher(FilterSearchResultsAction(searchState));
    } // end if dispatcher is function
  } // end function onSelectedSort
}
