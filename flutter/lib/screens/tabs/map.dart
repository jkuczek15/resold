import 'dart:async';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/search.dart';
import 'package:resold/state/actions/filter-search-results.dart';
import 'package:resold/state/search-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/list/product-list.dart';
import 'package:resold/widgets/loading.dart';
import 'package:resold/widgets/map/map-pin-pill.dart';
import 'package:resold/widgets/resold-search-bar.dart';
import 'package:resold/widgets/scroll/scrollable-filter-list.dart';

class MapPage extends StatefulWidget {
  final CustomerResponse customer;
  final SearchState searchState;
  final Position currentLocation;
  final List<Product> results;
  final Function dispatcher;

  MapPage(
      {CustomerResponse customer,
      SearchState searchState,
      Position currentLocation,
      List<Product> results,
      Function dispatcher,
      Key key})
      : customer = customer,
        searchState = searchState,
        currentLocation = currentLocation,
        results = results,
        dispatcher = dispatcher,
        super(key: key);

  @override
  MapPageState createState() =>
      MapPageState(this.customer, this.searchState, this.currentLocation, this.results, this.dispatcher);
}

class MapPageState extends State<MapPage> {
  CustomerResponse customer;
  SearchState searchState;
  Position currentLocation;
  final Function dispatcher;

  final Map<String, Marker> markers = new Map<String, Marker>();
  List<Product> results;
  double pinPillPosition = -100;
  Product selectedProduct = Product(name: '', thumbnail: '', price: '0');

  MapPageState(this.customer, this.searchState, this.currentLocation, this.results, this.dispatcher);

  @override
  Widget build(BuildContext context) {
    onBuild();
    return Stack(
      children: [
        Container(
          height: 760,
          child: GoogleMap(
            myLocationEnabled: true,
            myLocationButtonEnabled: false,
            mapToolbarEnabled: false,
            onMapCreated: (GoogleMapController controller) => onMapCreated(controller, currentLocation, results),
            initialCameraPosition: CameraPosition(
              target: LatLng(currentLocation.latitude, currentLocation.longitude),
              zoom: 9.0,
            ),
            markers: markers.values.toSet(),
            onTap: (LatLng location) {
              setState(() {
                pinPillPosition = -100;
              });
            },
          ),
        ),
        MapPinPill(
            customer: customer,
            currentLocation: currentLocation,
            dispatcher: dispatcher,
            pinPillPosition: pinPillPosition,
            selectedProduct: selectedProduct),
        Container(
          color: Colors.white.withOpacity(0.9),
          height: 131,
          child: ResoldSearchBar<Product>(
            placeHolder: results.length == 0 ? Center(child: Text('Your search returned no results.')) : null,
            textEditingController: searchState.textController,
            header: ScrollableFilterList(
                searchState: searchState, currentLocation: currentLocation, dispatcher: dispatcher),
            hintText: 'Search entire marketplace here...',
            searchBarPadding: EdgeInsets.symmetric(horizontal: 20),
            cancellationWidget: Icon(Icons.cancel),
            onCancelled: () async {
              dispatcher(FilterSearchResultsAction(searchState));
            },
            onSearch: (term) async {
              searchState.textController.text = term;
              dispatcher(FilterSearchResultsAction(searchState));
              return await Search.fetchSearchProducts(searchState, currentLocation.latitude, currentLocation.longitude);
            },
            onItemFound: (Product product, int index) {
              return ProductList.buildProductListTile(context, currentLocation, product, customer, dispatcher, index);
            },
            loader: Center(child: Loading()),
            suggestions: results,
            emptyWidget: results.length == 0
                ? Center(child: Text('Your search returned no results.'))
                : ProductList(
                    customer: customer,
                    searchState: searchState,
                    products: results,
                    currentLocation: currentLocation,
                    dispatcher: dispatcher),
          ),
        ),
      ],
    );
  } // end function build

  Future<void> onMapCreated(GoogleMapController controller, Position currentLocation, List<Product> products) async {
    setupMarkers(currentLocation, products);
  } // end function onMapCreated

  void setupMarkers(Position currentLocation, List<Product> products) {
    // clear markers
    markers.clear();

    // set product markers
    products.forEach((product) {
      if (product.latitude == currentLocation.latitude && product.longitude == currentLocation.longitude) {
        return;
      } // end if this my product

      // set product marker
      String markerId = product.id.toString();
      markers[markerId] = Marker(
          markerId: MarkerId(markerId),
          position: LatLng(product.latitude, product.longitude),
          icon: BitmapDescriptor.defaultMarkerWithHue(198),
          onTap: () {
            setState(() {
              selectedProduct = product;
              pinPillPosition = 0;
            });
          });
    });
  } // end function setupMarkers

  void onBuild() {
    results = widget.results;
    customer = widget.customer;
    searchState = widget.searchState;
    currentLocation = widget.currentLocation;
    setupMarkers(currentLocation, results);
  } // end function onBuild
}
