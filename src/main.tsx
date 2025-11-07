import { StrictMode } from 'react'
import ReactDOM from 'react-dom/client'
import { RouterProvider, createRouter } from '@tanstack/react-router'

// Import the generated route tree
import { routeTree } from './routeTree.gen'
import './styles.css'
import reportWebVitals from './reportWebVitals.ts'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { ConvexQueryClient } from '@convex-dev/react-query'
import { AuthLoading, ConvexProvider,  } from 'convex/react'
import { ConvexAuthProvider } from "@convex-dev/auth/react";
import { Authenticated, Unauthenticated } from 'convex/react'
import { AuthForm } from './components/authForm.tsx'
import { Loader2 } from 'lucide-react'

 import { ToastContainer, toast } from 'react-toastify';

// Create a new router instance
const router = createRouter({
  routeTree,
  context: {queryClient: new QueryClient()},
  defaultPreload: 'intent',
  scrollRestoration: true,
  defaultStructuralSharing: true,
  defaultPreloadStaleTime: 0,
})

const CONVEX_URL = (import.meta as any).env.VITE_CONVEX_URL!;
  if (!CONVEX_URL) {
    console.error("missing envar VITE_CONVEX_URL");
  }
  const convexQueryClient = new ConvexQueryClient(CONVEX_URL);

  const queryClient: QueryClient = new QueryClient({
    defaultOptions: {
      queries: {
        queryKeyHashFn: convexQueryClient.hashFn(),
        queryFn: convexQueryClient.queryFn(),
      },
    },
  });
  convexQueryClient.connect(queryClient);

// Register the router instance for type safety
declare module '@tanstack/react-router' {
  interface Register {
    router: typeof router
  }
}

// Render the app
const rootElement = document.getElementById('app')
if (rootElement && !rootElement.innerHTML) {
  const root = ReactDOM.createRoot(rootElement)
  root.render(
       <ConvexAuthProvider client={convexQueryClient.convexClient}>
        <QueryClientProvider client={queryClient}>
          <AuthLoading>
            <div className='grid m-20 justify-self-center text-center items-center'>
              <p className='text-center'>Auth loading</p>
              <Loader2 className="animate-spin " />
            </div>
          </AuthLoading>
          <Authenticated>
            <RouterProvider router={router} />
            <ToastContainer position="bottom-right" autoClose={5000} hideProgressBar={true} newestOnTop={false} closeOnClick rtl={false} pauseOnFocusLoss draggable pauseOnHover theme="light"/>
          </Authenticated>
          <Unauthenticated>
            <AuthForm />
          </Unauthenticated>
        </QueryClientProvider>
      </ConvexAuthProvider>
  )
}

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals(console.log)
