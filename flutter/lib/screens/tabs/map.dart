import 'dart:async';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/search.dart';
import 'package:resold/state/actions/fetch-search-results.dart';
import 'package:resold/state/actions/set-search-state.dart';
import 'package:resold/state/search-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
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
  final CustomerResponse customer;
  final SearchState searchState;
  final Position currentLocation;
  final Function dispatcher;
  final Map<String, Marker> markers = new Map<String, Marker>();
  List<Product> results;
  double pinPillPosition = -100;
  Product selectedProduct = Product(name: '', thumbnail: '', price: '0');

  MapPageState(this.customer, this.searchState, this.currentLocation, this.results, this.dispatcher);

  @override
  Widget build(BuildContext context) {
    results = widget.results;
    setupMarkers(currentLocation, results);
    return Stack(
      children: [
        Container(
          height: 760,
          child: GoogleMap(
            myLocationEnabled: true,
            myLocationButtonEnabled: false,
            mapToolbarEnabled: false,
            zoomControlsEnabled: false,
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
        MapPinPill(pinPillPosition: pinPillPosition, selectedProduct: selectedProduct, customerToken: customer.token),
        Container(
          color: Colors.white.withOpacity(0.9),
          height: 131,
          child: ResoldSearchBar<Product>(
            textEditingController: searchState.textController,
            header: ScrollableFilterList(currentLocation, searchState),
            hintText: 'Search entire marketplace here...',
            searchBarPadding: EdgeInsets.symmetric(horizontal: 20),
            cancellationWidget: Icon(Icons.cancel),
            onSearch: (term) async {
              searchState.textController.text = term;
              dispatcher(SetSearchStateAction(searchState));
              dispatcher(FetchSearchResultsAction());
              return await Search.fetchSearchProducts(searchState, currentLocation.latitude, currentLocation.longitude);
            },
            loader: Center(child: Loading()),
            suggestions: results,
            onItemFound: (Product product, int index) {
              return SizedBox();
            },
            emptyWidget: Center(child: Text('Your search returned no results.')),
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
}
