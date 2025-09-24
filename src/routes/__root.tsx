import { Outlet, createRootRoute } from '@tanstack/react-router'
//import { TanStackRouterDevtoolsPanel } from '@tanstack/react-router-devtools'
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
//import { TanstackDevtools } from '@tanstack/react-devtools'

import Header from '../components/Header'

export const Route = createRootRoute({
  component: () => (
    <>
      <Header />
      <div className="bg-gray-100 h-screen grid gap-5 items-center p-10">
      <Outlet />
      
      <ReactQueryDevtools buttonPosition="bottom-left" />
      </div>
    </>
  ),
})
