import NProgress from "nprogress";

function afterEachRoute() {
    // Complete the animation of the route progress bar.
    setTimeout(() => NProgress.done(), 500);
    // NProgress.done();       
}

export default afterEachRoute;