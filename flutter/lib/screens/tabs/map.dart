import 'dart:async';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:geolocator/geolocator.dart';
import 'package:liquid_pull_to_refresh/liquid_pull_to_refresh.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/search.dart';
import 'package:resold/state/actions/fetch-search-results.dart';
import 'package:resold/state/actions/set-search-state.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/state/search-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';
import 'package:resold/widgets/map/map-pin-pill.dart';
import 'package:resold/widgets/resold-search-bar.dart';
import 'package:resold/widgets/scroll/scrollable-filter-list.dart';

class MapPage extends StatefulWidget {
  MapPage({Key key}) : super(key: key);

  @override
  MapPageState createState() => MapPageState();
}

class MapPageState extends State<MapPage> {
  final Map<String, Marker> markers = {};
  double pinPillPosition = -100;
  Product selectedProduct = Product(name: '', thumbnail: '', price: '0');

  @override
  void initState() {
    super.initState();
  } // end function initState

  @override
  Widget build(BuildContext context) {
    return ViewModelSubscriber<AppState, SearchState>(
        converter: (state) => state.searchState,
        builder: (context, dispatcher, searchState) {
          return StreamBuilder<List<Product>>(
              initialData: searchState.initialProducts,
              stream: searchState.mapStream.stream,
              builder: (context, snapshot) {
                return ViewModelSubscriber<AppState, Position>(
                    converter: (state) => state.currentLocation,
                    builder: (context, dispatcher, currentLocation) {
                      if (snapshot.hasData) {
                        setupMarkers(currentLocation, snapshot.data);
                        // markers =
                      } // end if we have new products
                      return ViewModelSubscriber<AppState, CustomerResponse>(
                          converter: (state) => state.customer,
                          builder: (context, dispatcher, customer) {
                            return LiquidPullToRefresh(
                                height: 80,
                                springAnimationDurationInMilliseconds: 500,
                                onRefresh: () async {
                                  return await Search.fetchSearchProducts(
                                      searchState, currentLocation.latitude, currentLocation.longitude);
                                },
                                showChildOpacityTransition: false,
                                color: ResoldBlue,
                                animSpeedFactor: 5.0,
                                child: Stack(
                                  children: [
                                    Container(
                                      height: 650,
                                      child: GoogleMap(
                                        myLocationEnabled: true,
                                        myLocationButtonEnabled: false,
                                        mapToolbarEnabled: false,
                                        zoomControlsEnabled: false,
                                        onMapCreated: (GoogleMapController controller) => onMapCreated(
                                            controller, currentLocation, snapshot.hasData ? snapshot.data : []),
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
                                        pinPillPosition: pinPillPosition,
                                        selectedProduct: selectedProduct,
                                        customerToken: customer.token),
                                    Container(
                                      color: Colors.white.withOpacity(0.9),
                                      height: 130,
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
                                          return await Search.fetchSearchProducts(
                                              searchState, currentLocation.latitude, currentLocation.longitude);
                                        },
                                        loader: Center(child: Loading()),
                                        suggestions: snapshot.hasData ? snapshot.data : [],
                                        onItemFound: (Product product, int index) {
                                          return SizedBox();
                                        },
                                        emptyWidget: Center(child: Text('Your search returned no results.')),
                                      ),
                                    ),
                                  ],
                                ));
                          });
                    } // end builder
                    );
              });
        });
  } // end function build

  Future<void> onMapCreated(GoogleMapController controller, Position currentLocation, List<Product> products) async {
    setState(() {
      setupMarkers(currentLocation, products);
    });
  } // end function onMapCreated

  void setupMarkers(Position currentLocation, List<Product> products) {
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
  }
}
