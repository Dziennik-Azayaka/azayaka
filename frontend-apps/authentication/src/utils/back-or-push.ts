import type { Router } from "vue-router";

export function backOrPush(router: Router, routeName: string) {
    const backUrl = router.options.history.state["back"]?.toString();
    const backRoute = backUrl ? router.resolve(backUrl) : null;

    if (backRoute?.name === routeName) router.back();
    else router.push({ name: routeName });
}
